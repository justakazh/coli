
# COLI - Command Orchestration & Logic Interface

<p>
  <img src="https://raw.githubusercontent.com/justakazh/coli/refs/heads/main/public/assets/images/coli-logo.png" width="250"/>
  <img src="https://raw.githubusercontent.com/justakazh/coli/refs/heads/main/public/assets/images/coli-logo-2.png" width="250"/>
</p>

---

## Introduction

Running workflows, managing scopes, monitoring scans, and handling files entirely from the command line can quickly become **tedious, complex, and hard to visualize**.  

That’s why **COLI (Command Orchestrated & Logic Interface)** a modern **graphical interface** for [EWE (Execution Workflow Engine)](https://github.com/justakazh/ewe)  was created to **bring your CLI workflows to life**.  

With COLI, you can:
- **Visually create and connect tasks as nodes** using a drag-and-drop workflow editor
- **Manage scopes** and organize your targets efficiently
- **Monitor scans in real-time** with instant feedback
- **Access an interactive terminal** directly from your browser
- **Manage files** through a built-in file manager
- Enjoy **mobile-friendly support** for working anywhere

---

## 📺 Preview & Main Features

> *Below is a short preview video demonstrating COLI's core features and workflow:*


[![Watch the video](https://img.youtube.com/vi/QW4nusOm_GI/maxresdefault.jpg)](https://www.youtube.com/watch?v=QW4nusOm_GI)







**Main Features:**
- **Scope Management** – Add, edit, and remove target scopes.
- **Scan Management** – Launch scans and track their status in real time.
  - Import scan results from output files
  - Render CSV results into clean, readable tables
- **Workflow Builder** – Create task relationships visually with node-based design.
- **Interactive Terminal** – Fully functional web-based terminal (TTY).
- **File Manager** – Upload, download, edit, and delete files in one place.
- And more!

---

## ⚙️ Installation

```bash
git clone https://github.com/justakazh/coli
cd coli/
apt install composer php-gd php-dom php-xml php-sqlite3
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
pip install tabulate --break-system-packages
```

---

## 🔧 Configuration

Edit your `.env` file to configure COLI for your environment:

| Key | Description |
|--|--|
| APP_URL | Application base URL |
| AUTH_NAME | Login username |
| AUTH_EMAIL | Login email address |
| AUTH_PASSWORD | Login password |
| FILE_MANAGER_USER | File manager username |
| FILE_MANAGER_PASS | File manager password |
| HOME_PATH | Home directory of the user running COLI |
| TOOLS_PATH | Path(s) to installed tools (`/usr/bin`, `/home/<user>/go/bin`, etc.)|
| HUNT_PATH | Directory to store scan results (e.g., `/home/<user>/hunting`) |
| EWE_CLI_OPTIONS | CLI arguments for EWE ([see reference](https://github.com/justakazh/ewe/tree/main#cli-options)) |
| DB_CONNECTION | Database type (`sqlite`, `mysql`, `pgsql`, etc.) |
| DB_DATABASE | Path to SQLite database file (if using SQLite) |

---

## 🚀 Running COLI

```bash
php artisan serve
```
Access it in your browser at:  
`http://127.0.0.1:8000`

---

## 🌐 Deployment (Optional)

To run COLI behind Apache2, Nginx, or another web server, see:

- [How to deploy Laravel with Apache & MySQL](https://adeyomoladev.medium.com/how-to-deploy-a-laravel-app-using-apache-and-mysql-4910a07f9a0c)  
- [Laravel install step-by-step on Ubuntu](https://dev.to/abstractmusa/laravel-installs-in-ubuntu-step-by-step-3jom)  
- [Laravel deployment with Apache on Ubuntu 24.04](https://docs.vultr.com/how-to-deploy-laravel-with-apache-on-ubuntu-24-04)

**Example Apache2 Configuration:**

```apache
<VirtualHost *:80>
    ServerName example.com
    Redirect permanent / https://example.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName example.com
    DocumentRoot [document_root] # change this

    SSLEngine on
    SSLCertificateFile [ssl public key location] # change this
    SSLCertificateKeyFile [ssl private key location] # change this

    <Directory [document_root]>
        AllowOverride All
        Require all granted
    </Directory>

    # WebSocket + fallback polling
    ProxyRequests Off
    ProxyPreserveHost On

    RewriteEngine On
    RewriteCond %{HTTP:Upgrade} =websocket [NC]
    RewriteRule ^/socket.io/(.*) ws://127.0.0.1:8080/socket.io/$1 [P,L]

    ProxyPass /socket.io/ http://127.0.0.1:8080/socket.io/
    ProxyPassReverse /socket.io/ http://127.0.0.1:8080/socket.io/
</VirtualHost>
```

💡 *We welcome contributions for creating a Dockerfile!*

---

## 🛠️ Technology Stack

COLI is powered by:

- **Laravel 12.x** (Backend)
- **Python 3** (CLI integration & processing)
- **MermaidJS** (Visualization)
- **Drawflow** (Node-based workflow editor)
- **Xterm.js** (Web terminal)
- **Tiny File Manager** (File management)

---

## 💻Working Method

<image > 

## ⚡ Quick Start Example

```json
{
    "name": "Simple Scan",
    "slug": "simple-scan",
    "description": "simple subdomain scan, collect url, and nuclei scan.",
    "tags": "nuclei,subfinder,httpx,waybackurls",
    "category": "single",
    "tasks": [
        {
            "name": "Subdomain Finder",
            "description": "subdomain finder using subfinder",
            "result": "subdomain_result.txt",
            "command": "subfinder -d {target} -o {result}",
            "wait_all": false,
            "tasks": [
                {
                    "name": "http check",
                    "description": "checking for http\/s with httpx",
                    "result": "http_service.txt",
                    "command": "httpx -l {parent_result} -o {result}",
                    "wait_all": false,
                    "tasks": [
                        {
                            "name": "Nuclei Scan",
                            "description": "",
                            "result": "nuclei_result.txt",
                            "command": "nuclei -list {parent_result} -o {result}",
                            "wait_all": false,
                            "tasks": []
                        }
                    ]
                }
            ]
        },
        {
            "name": "Collecting URLs",
            "description": "collecting url from wayback machine",
            "result": "urls.txt",
            "command": "echo {target} | waybackurls | anew {result}",
            "wait_all": false,
            "tasks": []
        }
    ]
}
```

1.  **Add New Workflow** and select **Json** for  Workflow Type  
2. fill required field, and paste json script.  
3. **Run** the workflow and monitor real-time progress.
---

## 🤝 Contributing

We welcome pull requests for:
- Bug fixes
- New features
- Performance optimizations
- Documentation improvements

---

## 📜 License

This project is licensed under the **MIT License** — you are free to use, modify, and distribute it with attribution.
