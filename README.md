
# BlueBank

Sistema financiero core de gestión de cuentas de ahorro.

## Lógica de implementación

La idea principal es tener como base de la solución un banco, el cual tiene activos, el banco es administrado por un empleado que registra a los clientes y las cuentas.

El empleado del banco tiene acceso al listado de clientes puede crear y/o editar un cliente, puede crear y/o editar una o varias cuentas, además puede depositar y/o retirar dinero de alguna cuenta de un cliente también puede ver el detalle de las transacciones de una cuenta

El cliente del banco tiene acceso el listado sus cuentas donde puede depositar y/o retirar dinero de alguna cuenta también puede ver el detalle de las transacciones de una cuenta

## Instalación

El proyecto se divide en dos partes **backend** y **frontend**

### Requisitos backend
- PHP >= 7.3
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Composer https://getcomposer.org/

### Requisitos frontend
- Node JS https://nodejs.org/en/
- NPM https://www.npmjs.com/get-npm

### backend
Desde consola en la raíz del proyecto hacer:

```bash  
# Entrar a la carpeta /backend  
cd backend  
```  

```bash  
# Instalar dependencias  
composer install  
  
# Copiar archivo de configuraciones  
cp .env.example .env  
  
# Generar unique key del proyecto  
php artisan key:generate  
```  

```bash  
# Crear base de datos  
php artisan create:database  
  
# Crear tablas de base de datos y generar datos iniciales  
php artisan migrate --seed  
```  

```bash  
# Instalar claves de cifrado para Laravel Passport  
php artisan passport:install  
```  

```bash  
# Levantar el servicio  
php -S localhost:8000 -t public  
```  

### frontend
Desde consola en la raíz del proyecto hacer:

```bash  
# Entrar a la carpeta /backend  
cd frontend  
```  

```bash  
# Instalar dependencias  
npm install  
```  

```bash  
# Levantar el servicio  
npm run dev --watch  
```  

## Servidor (Nginx)
### Windows local

```bash  
# Ir la carpeta de sitios de configuración de nginx  
cd nginx/conf  
  
# Editar el archivo de configuración  
vim nginx.conf  
```  


```nginx  
# Configuracion del archivo nginx.conf  
# Dentro de http { } agregar:  
server {  
   listen 80; server_name localhost;
   
   location / {  
      proxy_pass http://127.0.0.1:9527;
   }
     
   location /api/v1 {
      proxy_pass http://localhost:8000; 
   }
}  
```  

### Linux server (PRODUCCIÓN)

```bash  
# Ir la carpeta frontend  
cd frontend  
  
# Compilar  
npm run build:prod  
```  

```bash  
# Ir la carpeta de sitios de nginx  
cd /etc/nginx/sites-available  
  
# Editar sitio por defecto  
vim default  
```  

```nginx  
# Configuracion del archivo default  
server {  
 server {
    listen 80;
    server_name localhost;
    index index.html;

    location /api/v1 {
        proxy_pass         http://localhost:8000;
        proxy_redirect     off;
        proxy_set_header   Host $host;
        proxy_set_header   X-Real-IP $remote_addr;
        proxy_set_header   X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Host $server_name;
        proxy_set_header   HTTP_AUTHORIZATION $http_authorization;
    }
    location /{
        root /var/www/BlueBank/frontend/dist; # path of angular application upto dist
        try_files $uri $uri/ /index.html;
    }
}

server {
    listen 8000;
    root /var/www/BlueBank/backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```  

## Tecnologías usadas
### Backend

- **Laravel Lumen**  
  Lumen es una versión más liviana de Laravel y orientado más a la creación de APIs y  
  microservicios.

  > Dado el alcance del proyecto y los requerimientos planteados decidí usar este framework para desarrollar las APIs y ocupar solo lo necesario de Laravel para ello, sin extra de dependencias, de vistas u otras tecnologías que no iba a usar en el backend.


- **Laravel passport**  
  Laravel Passport es el módulo oficial de Laravel que nos ayuda a la implementación de servicios de autenticación haciendo uso del protocolo OAuth2

  > Dadas las necesidades del proyecto y al tratarse de un sistema financiero se espera un nivel de seguridad mayor para la protección de las transacciones, es por ello es que implemente OAuth2 con passport para la autenticación con tokens y la protección de las request

- **SQLite**  
  SQLite es un sistema de gestión de base de datos relacional, contenida en una biblioteca muy pequeña.
  > Dado el alcance del proyecto y los requerimientos planteados decidí usar SQLite porque la base de datos es pequeña y me pareció más práctico de implementar en este caso, además de que cumple con los requerimientos de una base de datos relacional.

### Frontend

- **Vue.js**
- Vue.js es un framework de JavaScript de código abierto para la construcción de interfaces de usuario y aplicaciones SPA.

> Vuejs es un framework bastante simple de usar, pero robusto que permite un desarrollo rapid de una application SPA, es por eso que decide usarlo en este caso para el sistema financiero requerido.

- **ElementUI** https://element.eleme.io/#/es  
  Es un kit completo para construir interfaces reutilizando componentes de VueJs

- **VueX**  
  Es una librería para gestión del estado (State Management) de aplicaciones Vue.js

- **Vue Router**  
  Es la librería de enrutamiento oficial de Vue.js para construir la navegacion a traves de enlaces en una SPA

- **Axios**  
  Es una librería JavaScript para realizar peticiones HTTP (Ajax) para consumir APIs REST

## Arquitectura de desarrollo

Utilicé TDD para el desarrollo usando el patrón de diseño por defecto propuesto por Laravel

## Mejoras si tuviese tiempo

- **Base de datos**  
  Haría una estructura diferente, puesto que un banco tiene sedes, y las sedes estan en
  diferentes ubicaciones, son administrados por diferentes empleados y los clientes abren
  sus cuentas en las sedes del banco

- **Roles y permisos**  
  Implementar sistema de roles y permisos
  
- **Gestión de sesiones de empleados y cliente**  
  Cerrar la sesión después de X tiempo debido a un tema de seguridad
  y permitir solo una sesión a la vez

## Diagrama ER Base de datos BlueBank
![Base de datos](https://i.ibb.co/VCsDzB5/Blue-Bank-BD.png)


## Video demostrativo
[![video](https://i9.ytimg.com/vi_webp/xHy2C7iDmcE/mqdefault.webp?time=1618945500000&sqp=CNzL_IMG&rs=AOn4CLCnHx4aZo_JXJWiYM4Evn_4qe-LLA)](https://www.youtube.com/watch?v=xHy2C7iDmcE)
