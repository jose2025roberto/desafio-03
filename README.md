# Sistema de Asistencia de Tutorías sabatinas

Este sistema permite a los tutores registrar la asistencia de sus estudiantes y generar reportes trimestrales en PDF.

---

## Instrucciones de instalación y uso

### Requisitos

- PHP 7.4 o superior
- Servidor web (XAMPP)
- MySQL
- Navegador
- instalar Composer (composer require mpdf/mpdf) para uso de la libreria para PDF. en consola dentro de proyecto

### Instalación

1. Clona o descarga el repositorio.
2. Crea una base de datos en MySQL.
3. Importa el archivo `academia_sabatina.sql` para generar las tablas necesarias.
4. Configura la conexión en `config/conexion.php` con tus datos de host, usuario, contraseña y base de datos.
5. Coloca el proyecto en el servidor web (`htdocs` si usas XAMPP).
6. Accede desde el navegador:  
   `http://localhost/DESAFIOPRACTICO_Academia/login.php`

### Uso

- Inicia sesión como tutor. (GA001) contraseña(tutor123)
- Solo se puede tomar asistencia los **sábados entre 8:00 a.m. y 11:00 a.m.**
- Desde el panel, puedes:
  - Registrar asistencia
  - Seleccionar tipo de asistencia (presente/ausente)
  - Agregar aspectos observados (puntualidad, conducta, etc.)
  - Ver historial de asistencia
  - Generar PDF trimestral por estudiante

---

- inicia session como Administrador.
  (admin) contraseña (admin123)
- El administrador puede gestionar los grupos académicos del sistema. Las funciones principales incluyen:

- Crear nuevos grupos asignando un tutor responsable.

- Visualizar grupos existentes, junto con su tutor asignado.

- Eliminar grupos cuando ya no sean necesarios.

- Asignar estudiantes a uno o más grupos.

- Visualizar estudiantes inscritos por grupo y eliminar a estudiantes específicos si es necesario.

## Estructura de clases y archivos

### Archivos principales

- **`config/conexion.php`**  
  Conexión a la base de datos.

- **`login.php`**  
  Pantalla de inicio de sesión del tutor.

- **`dashboard_tutor.php`**  
  Panel de control del tutor. Muestra grupo asignado, estudiantes, y formulario de asistencia.

- **`logout.php`**  
  Cierra sesión y redirige al login.

- **`ver_pdf_trimestral.php`**  
  Genera reporte PDF con los datos del estudiante.

### Tablas en la base de datos

- `tutores`: información de los tutores (nombre, código, usuario, contraseña).
- `grupos`: datos del grupo asignado a cada tutor.
- `estudiantes`: lista de estudiantes con código único.
- `grupo_estudiantes`: asociación entre grupos y estudiantes.
- `asistencias`: registros de asistencia por estudiante y fecha.
- `aspectos`: tipos de aspectos observables (ej. participación, respeto).
- `aspecto_estudiante`: aspectos observados por fecha y estudiante.

---

## Créditos

Autor: [Tu Nombre Aquí]  
Contacto: [tuemail@ejemplo.com]  
Fecha: 2025

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Puedes modificarlo y usarlo libremente.
