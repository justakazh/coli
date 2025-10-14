

<p align="">
  <img src="https://raw.githubusercontent.com/justakazh/coli/refs/heads/main/public/assets/img/logo.png" width="300"/>
</p>

# COLI - Command Orchestration & Logic Interface

    
---

## Introduction

Running workflows, managing scopes, monitoring scans, and handling files entirely from the command line can quickly become **tedious, complex, and hard to visualize**. That’s why **COLI (Command Orchestrated & Logic Interface)**  was created to **bring your CLI workflows to life**.  

With COLI, you can:
- **Visually create and connect tasks as nodes** using a drag-and-drop workflow editor
- **Manage scopes** and organize your targets efficiently
- **Monitor scans in real-time** with instant feedback
- **Access an interactive terminal** directly from your browser
- **Explore files** through a built-in explorer
- Enjoy **mobile-friendly support** for working anywhere

---


## 🚀 Running COLI

```bash
git clone https://github.com/justakazh/coli
cd coli
docker build -t coli .
docker run -p 80:80 -p 5000:5000 --name coli coli
```

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
    RewriteRule ^/socket.io/(.*) ws://127.0.0.1:5000/socket.io/$1 [P,L]

    ProxyPass /socket.io/ http://127.0.0.1:5000/socket.io/
    ProxyPassReverse /socket.io/ http://127.0.0.1:5000/socket.io/
</VirtualHost>
```

---

## 🛠️ Technology Stack

COLI is powered by:

- **Laravel 12.x** (Backend)
- **Python 3** (CLI integration & processing)
- **MermaidJS** (Visualization)
- **Drawflow** (Node-based workflow editor)
- **Xterm.js** (Web terminal)

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
