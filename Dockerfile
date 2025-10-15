FROM ubuntu:latest AS base


#---------------------------------------
# CREATING USER AND SET PASSWORD (PLEASE CHANGE THIS)
#---------------------------------------
RUN useradd -m -s /bin/bash coli && \
    echo 'coli:coliuserpassword' | chpasswd && \
    echo 'root:rootuserpassword' | chpasswd



#---------------------------------------
# COLI INSTALLATION
#---------------------------------------
# Installing dependencies
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    golang-go \
    php-common \
    php-dom php-xml php-sqlite3 composer git curl wget nano



#change to home directory
WORKDIR /home/coli/

#creating directories
RUN mkdir -p app
RUN mkdir -p workdir
RUN mkdir -p workdir/database
RUN mkdir -p workdir/workflows


#setting for app
COPY . app

#setting for workdir
RUN touch workdir/database/database.sqlite
RUN cp app/examples/simple-scan.json workdir/workflows/simple-scan.json


#change to app directory
WORKDIR /home/coli/app

#SETUP app/.env
RUN cp .env.example .env

RUN sed -i 's|^WORKDIR=.*|WORKDIR=/home/coli/workdir|' .env
RUN sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|' .env
RUN sed -i 's|^DB_DATABASE=.*|DB_DATABASE=/home/coli/workdir/database/database.sqlite|' .env

## Coli Credential and PATH (PLEASE CHANGE THIS)
RUN sed -i 's|^AUTH_NAME=.*|AUTH_NAME=coli|' .env
RUN sed -i 's|^AUTH_EMAIL=.*|AUTH_EMAIL=coli@example.com|' .env
RUN sed -i 's|^AUTH_PASSWORD=.*|AUTH_PASSWORD=colidashboard|' .env
RUN sed -i 's|^USER_HOME=.*|USER_HOME=/home/coli|' .env
RUN sed -i 's|^USER_PATH=.*|USER_PATH=/home/coli/go/bin:/home/coli/.local/bin|' .env


#Change owner and permission
RUN chown coli:coli /home/coli/app -R && chmod 700 /home/coli/app -R
RUN chown coli:coli /home/coli/workdir -R && chmod 700 /home/coli/workdir -R


#change to coli user 
USER coli

# Install Composer dependencies
RUN composer install

# Setup key and migrate
RUN php artisan key:generate
RUN php artisan migrate:fresh --seed


# Setup Python dependencies
RUN pip install flask flask_socketio --break

#---------------------------------------
# TOOLS INSTALLATION
#---------------------------------------
# If tools installation needs root access, type command here
USER root
RUN apt install nmap -y


#If tools installation doesn't need root access, type command here
USER coli
RUN go install -v github.com/projectdiscovery/httpx/cmd/httpx@latest
RUN go install -v github.com/projectdiscovery/subfinder/v2/cmd/subfinder@latest


#---------------------------------------
# RUN COLI
#---------------------------------------

EXPOSE 80 5000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]  