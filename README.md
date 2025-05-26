# SiGestActivos

Sistema de Gestión de Activos.

## Descripción

Este proyecto es un sistema de gestión de activos desarrollado en PHP. Permite la administración de activos, incluyendo categorías, estados, movimientos, proveedores, roles, sucursales y usuarios.

## Características

*   Gestión de Activos: Permite crear, editar y eliminar activos.
*   Gestión de Categorías: Permite definir y administrar categorías de activos.
*   Gestión de Estados: Permite definir y administrar los estados de los activos.
*   Gestión de Movimientos: Permite registrar y administrar los movimientos de los activos.
*   Gestión de Proveedores: Permite administrar la información de los proveedores.
*   Gestión de Roles: Permite definir y administrar los roles de usuario.
*   Gestión de Sucursales: Permite administrar la información de las sucursales.
*   Gestión de Usuarios: Permite crear, editar y eliminar usuarios.

## Estructura del Proyecto

El proyecto sigue una arquitectura MVC (Modelo-Vista-Controlador).

*   `app/config`: Contiene los archivos de configuración del proyecto.
*   `app/controllers`: Contiene los controladores del proyecto.
*   `app/models`: Contiene los modelos del proyecto.
*   `app/views`: Contiene las vistas del proyecto.
*   `public`: Contiene los archivos públicos del proyecto (CSS, JavaScript, imágenes, etc.).

## Base de Datos

El proyecto utiliza una base de datos SQL Server. El esquema de la base de datos se encuentra en `app/database/bdActivos.sql`. La conexión a la base de datos se configura en el archivo `app/config/configuracion.php`.

## Instalación

1.  Importar el esquema de la base de datos `app/database/bdActivos.sql` a una base de datos SQL Server.
2.  Configurar la conexión a la base de datos en `app/config/configuracion.php` con la información de su servidor SQL Server.
3.  Subir los archivos del proyecto a un servidor web.

## Requisitos

*   PHP 7.0 o superior
*   SQL Server 2012 o superior

## Configuración

El archivo `app/config/configuracion.php` contiene la configuración de la base de datos y otras configuraciones del proyecto.
