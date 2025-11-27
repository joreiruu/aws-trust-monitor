import { SNSClient, PublishCommand } from "@aws-sdk/client-sns";
import { DynamoDBClient } from "@aws-sdk/client-dynamodb";
import { DynamoDBDocumentClient, PutCommand, QueryCommand } from "@aws-sdk/lib-dynamodb";

const snsClient = new SNSClient({ region: "ap-southeast-2" });
const dbClient = new DynamoDBClient({ region: "ap-southeast-2" });
const docClient = DynamoDBDocumentClient.from(dbClient);

export const handler = async (event) => {
  const targetUrl = 'https://www.google.com';
  const tableName = 'TrustMonitorLogs';
  const snsTopicArn = 'arn:aws:sns:ap-southeast-2:177815087725:SiteDownAlerts';
  
  const timestamp = new Date().toISOString();

  try {
    // 1. CHECK HEALTH
    const response = await fetch(targetUrl);
    const isOnline = response.status === 200;
    const statusText = isOnline ? 'ONLINE' : 'ISSUE';

    // 2. ALERTING (Email if down)
    if (!isOnline) {
       const params = {
        Message: `URGENT: ${targetUrl} is DOWN.`,
        Subject: "⚠️ CRITICAL: Monitor Alert",
        TopicArn: snsTopicArn,
      };
      await snsClient.send(new PublishCommand(params));
    }

    // 3. SAVE TO DATABASE (DynamoDB)
    await docClient.send(new PutCommand({
      TableName: tableName,
      Item: {
        target_url: targetUrl,
        timestamp: timestamp,
        status: statusText,
        http_code: response.status
      }
    }));

    // 4. FETCH HISTORY (Get last 5 logs)
    const historyData = await docClient.send(new QueryCommand({
      TableName: tableName,
      KeyConditionExpression: "target_url = :url",
      ExpressionAttributeValues: { ":url": targetUrl },
      ScanIndexForward: false, // Reverse order (newest first)
      Limit: 5
    }));

    // 5. RETURN EVERYTHING
    return {
      statusCode: 200,
      body: JSON.stringify({
        current: {
          target: targetUrl,
          status: statusText,
          http_code: response.status,
          timestamp: timestamp
        },
        history: historyData.Items
      }),
    };

  } catch (error) {
    console.error(error);
    return { statusCode: 500, body: JSON.stringify({ error: error.message }) };
  }
};