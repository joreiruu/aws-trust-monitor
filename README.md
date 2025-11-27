# ☁️ Serverless Trust Monitor (Enterprise Edition)

**A Full-Stack Serverless Application for real-time website monitoring, automated alerting, and historical data logging.**

This project demonstrates a production-ready serverless architecture connecting **AWS Lambda**, **DynamoDB**, and **SNS** to a **PHP Frontend**, fully automated via a **CI/CD Pipeline**.

![Dashboard Preview](dashboard.png)

## 🚀 Key Features
* **Real-Time Monitoring:** Checks website status (HTTP 200 OK) on demand.
* **🔥 Autonomous Alerting:** Uses **AWS EventBridge** to schedule checks every 5 minutes and **AWS SNS** to send urgent email alerts if the target site goes down.
* **💾 Data Persistence:** Logs every single health check into **AWS DynamoDB** for historical analysis.
* **⚡ Serverless Backend:** Built on AWS Lambda (Node.js) to scale to zero cost when idle.
* **🔄 CI/CD Automation:** Fully automated deployment pipeline using **GitHub Actions**.

## 🏗 Architecture
The system follows a decoupled microservices architecture:

`User/Scheduler` → `AWS Lambda (Node.js)` → `Target Website`
                                      ↓
                            ---------------------
                            ↓         ↓         ↓
                     `DynamoDB`     `SNS`    `Frontend (PHP)`
                     (Logging)    (Alerts)    (Visualization)

## 🛠 Tech Stack
| Component | Technology | Role |
| :--- | :--- | :--- |
| **Backend** | AWS Lambda (Node.js 20.x) | Core Business Logic |
| **Database** | AWS DynamoDB | NoSQL History Logging |
| **Alerting** | AWS SNS | Critical Email Notifications |
| **Frontend** | PHP 8.x + HTML/CSS | Data Visualization Dashboard |
| **DevOps** | GitHub Actions | CI/CD Automated Deployment |
| **IDE** | VS Code | Development Environment |

## 📸 Project Highlights

### 1. The Automation (CI/CD)
I moved away from manual deployments. Every push to the `main` branch triggers a GitHub Action that:
1.  Tests the code integrity.
2.  Zips the Node.js function.
3.  Authenticates via IAM Roles.
4.  Deploys the update to AWS Lambda automatically.

### 2. The Database (DynamoDB)
Instead of transient data, the system persists health checks. The backend writes a log entry with `Timestamp` and `HTTP Code` to DynamoDB, allowing the Frontend to fetch and display the "Recent History" table.

### 3. Reliability (Self-Healing)
The system includes error handling logic. If the Lambda function fails to connect (e.g., DNS error), it catches the exception and triggers an immediate SNS alert to the administrator.

## 🚀 How to Run Locally

### Prerequisites
* PHP 8.x
* Internet connection (to reach AWS API)

### Installation
1.  Clone the repository.
2.  Navigate to the `frontend` directory.
3.  Start the local server:
    ```bash
    php -S localhost:8000
    ```
4.  Open `http://localhost:8000` in your browser.

---
*Built by [Your Name] as a showcase of Cloud Engineering skills.*