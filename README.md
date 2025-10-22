# 🎬 PROYECTO VIDEOCLUB

## 📋 ¿Qué es este proyecto?
Es un programa para gestionar un videoclub, como los de antes donde se alquilaban películas y videojuegos. Está hecho en PHP usando programación orientada a objetos.

## 🏗️ ¿Cómo está organizado?
Videoclub/
├── app/ ← Aquí está todo el código
├── test/ ← Para hacer pruebas
├── vendor/ ← Librerías externas
└── inicio.php ← Archivos para probar
## 🎯 ¿Qué puede hacer este programa?

### 👥 Gestión de Clientes
- **Registrar nuevos clientes** en el videoclub
- **Controlar cuántas películas** puede alquilar cada cliente a la vez
- **Ver el historial** de lo que ha alquilado cada cliente
- **Devolver productos** cuando el cliente los trae de vuelta

### 🎞️ Gestión de Productos
- **Añadir nuevos productos**: películas en VHS, DVDs y videojuegos
- **Mostrar información** de cada producto (duración, idiomas, consola...)
- **Saber qué está disponible** y qué está alquilado

### 🔄 Operaciones de Alquiler
- **Alquilar un producto** a un cliente
- **Alquilar varios productos** a la vez
- **Comprobar automáticamente** si hay problemas:
  - ¿El producto ya está alquilado?
  - ¿El cliente tiene cupo disponible?
  - ¿Existe el producto y el cliente?

## 🛡️ Sistema de Errores Inteligente
El programa detecta problemas y avisa con mensajes claros:

- **"Este producto ya está alquilado"**
- **"El cliente no puede alquilar más productos"** 
- **"No encontramos este producto"**
- **"No encontramos este cliente"**

🔄 Carga Automática
El programa carga automáticamente los archivos necesarios, no hace falta importarlos uno por uno.

📁 Organización con Namespaces
El código está bien organizado como si fuera una biblioteca con secciones.

🧪 Archivos para Probar
inicio.php → Prueba los productos (DVDs, juegos...)

inicio2.php → Prueba clientes y alquileres simples

inicio3.php → Prueba TODO el sistema completo

👥 Desarrolladores
Antonio Pérez Carrasco

Iker Clemente Quijada

🎓 ¿Para qué se hizo?
Este proyecto fue creado para aprender:

Cómo programar con objetos en PHP

Cómo organizar proyectos grandes

Cómo manejar errores correctamente

Cómo trabajar en equipo

## 🚀 Instalación Rápida

### Paso 1: Descargar el proyecto
```bash
git clone https://github.com/anpeca/VIDEOCLUB.git
Paso 2: Colocar en el servidor
Copia la carpeta VIDEOCLUB a htdocs (si usas XAMPP) o www (si usas WAMP)

Paso 3: Probar que funciona
Abre tu navegador web

Ve a: http://localhost/VIDEOCLUB/inicio3.php

¡Deberías ver la página del videoclub funcionando!

📄 Licencia
Proyecto educativo del IES Valle del Jerte (2DAW)

Plasencia, 2025

Nota: Este proyecto fue desarrollado con asistencia de DeepSeek para resolver errores y mejorar el código.
