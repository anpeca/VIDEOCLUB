# 🎬 PROYECTO VIDEOCLUB

## 📋 Descripción
Este proyecto es un sistema de gestión para administrar la información de un videoclub, está realizado en PHP pensado con POO (Programación Orientada a Objetos). Tiene la capacidad de administrar clientes, productos (cintas de video, DVD, juegos) y realizar operaciones de alquiler.

## 🏗️ Estructura del Proyecto
Videoclub/
├── app/
│ └── Dwes/
│ └── ProyectoVideoclub/
│ ├── Util/
│ │ ├── VideoclubException.php
│ │ ├── SoporteYaAlquiladoException.php
│ │ ├── CupoSuperadoException.php
│ │ ├── SoporteNoEncontradoException.php
│ │ └── ClienteNoEncontradoException.php
│ ├── Soporte.php (clase abstracta)
│ ├── Cliente.php
│ ├── CintaVideo.php
│ ├── Dvd.php
│ ├── Juego.php
│ └── Videoclub.php
├── test/
├── vendor/
├── autoload.php
├── inicio.php
├── inicio2.php
└── inicio3.php

text

## 🎯 Características Principales

### ✅ Características Implementadas
- **POO Completo**: Herencia, encapsulación, polimorfismo
- **Namespaces**: Organización con `Dwes\ProyectoVideoclub`
- **Autoload**: Carga automática de clases
- **Encadenamiento de métodos**: API fluida
- **Manejo de excepciones**: Sistema personalizado de errores
- **Tipado estricto**: PHP 7.4+ con tipos definidos

### 📊 Modelo de Clases
Soporte (abstract)
├── CintaVideo
├── Dvd
└── Juego

Cliente
Videoclub

text

## 🚀 Instalación y Uso

### Requisitos
- PHP 7.4 o superior
- Servidor web (XAMPP, WAMP, etc.)
- Git (opcional)

### Configuración
1. Clonar el repositorio
2. Colocar en directorio web (htdocs/www)
3. Acceder via navegador a `inicio3.php`


💡 Funcionalidades
Gestión de Productos
✅ Añadir cintas de video, DVDs y juegos

✅ Listado de productos disponibles

✅ Información detallada de cada producto

Gestión de Clientes
✅ Registro de socios

✅ Sistema de alquiler con límites

✅ Devolución de productos

✅ Historial de alquileres

Operaciones de Alquiler
✅ Alquiler individual (alquilarSocioProducto)

✅ Alquiler múltiple (alquilarSocioProductos)

✅ Validación de disponibilidad

✅ Control de cupos máximos

🛡️ Sistema de Excepciones
El proyecto incluye un sistema personalizado de excepciones:

VideoclubException (padre)

SoporteYaAlquiladoException

CupoSuperadoException

SoporteNoEncontradoException

ClienteNoEncontradoException

🔧 Características Técnicas
Patrones Implementados
Method Chaining: Encadenamiento de métodos

Exception Handling: Manejo personalizado de errores

Autoloading: Carga automática de clases

Namespacing: Organización del código

Buenas Prácticas
Cada clase en archivo separado

Tipado estricto en métodos y propiedades

Documentación en código

Separación de responsabilidades

📝 Archivos de Prueba
inicio.php: Pruebas básicas de productos

inicio2.php: Pruebas de clientes y alquileres

inicio3.php: Prueba completa del sistema

🏷️ Versiones
v0.331: Implementación de namespaces

v0.337: Sistema de excepciones y autoload

👥 Desarrollo
Desarrolladores:

Antonio Pérez Carrasco

Iker Clemente Quijada

Proyecto desarrollado como ejercicio educativo para el aprendizaje de:

Programación Orientada a Objetos en PHP

Patrones de diseño

Manejo de excepciones

Organización de proyectos

📄 Licencia
Proyecto educativo - 2DAW IES Valle del Jerte

Plasencia, 2025



Este README se ha proporcionado con inteligencia artificial DeepSeek, también se utilizó para asistir en la  solución de errores.
