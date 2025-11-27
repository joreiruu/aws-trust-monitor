export const handler = async (event) => {
  // 1. Define the target URL to monitor
  const targetUrl = 'https://www.google.com';

  try {
    // 2. Try to fetch the URL
    const response = await fetch(targetUrl);
    
    // 3. Check if status is 200 (OK)
    const isOnline = response.status === 200;

    const result = {
      target: targetUrl,
      status: isOnline ? 'SYSTEM OPERATIONAL' : 'SYSTEM CRITICAL',
      http_code: response.status,
      timestamp: new Date().toISOString()
    };

    // 4. Return success response
    return {
      statusCode: 200,
      body: JSON.stringify(result),
    };

  } catch (error) {
    // 5. Handle errors (Site is down or DNS failure)
    return {
      statusCode: 500,
      body: JSON.stringify({
        target: targetUrl,
        status: 'OFFLINE',
        error: error.message
      }),
    };
  }
};