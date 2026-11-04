# Mueblería Imperial

**Proyecto Final Fullstack — Aplicación web para la gestión de una mueblería**

Mueblería Imperial es una aplicación web en desarrollo para administrar un catálogo de muebles, inventario, ventas, pedidos, pagos, entregas y servicios de reparación. Contempla ventas por internet y ventas presenciales registradas por empleados, con inventario compartido.

> **Estado actual (4 de noviembre de 2026):** se completó la preparación inicial del entorno y la estructura base de la aplicación. **Todavía no están implementados** los módulos de negocio descritos más abajo. Este README documenta el sistema previsto y cómo ejecutar la base existente.

## 1. Alcance y objetivos

El proyecto es **exclusivamente web**; no incluye una aplicación Android.

### Funcionalidades previstas

- **Usuarios y acceso:** roles Administrador, Ventas, Taller y Cliente. Los clientes podrán registrarse; el administrador creará las cuentas de empleados.
- **Catálogo e inventario:** categorías, muebles y existencias compartidas entre ventas web y presenciales.
- **Pedidos:** carrito con totales; envío de solicitudes de compra; verificación de anticipo por un empleado antes de reservar existencias; salida definitiva del inventario al entregar.
- **Pagos:** anticipo mínimo configurable; valor inicial previsto de **25 % para ventas** y **50 % para reparaciones**. Entrega únicamente después de completar el pago; sin crédito.
- **Cancelaciones y devoluciones:** cancelación automática antes de preparación y autorización de un empleado después; devoluciones totales o parciales con motivo e importe.
- **Entregas:** una entrega por pedido, recogida en tienda o entrega a domicilio, con seguimiento manual.
- **Reparaciones:** solicitudes con varias piezas, evaluación gratuita, presupuesto aceptado y anticipo verificado antes de iniciar; pago completo antes de entregar; recepción y entrega en tienda o domicilio; aceptación de clientes externos.
- **Administración:** panel de control y reportes de ventas, inventario y reparaciones.

Estas son **reglas y requisitos acordados**, no una afirmación de que las funcionalidades ya estén programadas. No se implementarán tiempos de vigencia para solicitudes o presupuestos.

## 2. Tecnologías

| Componente | Tecnología | Versión conocida / observaciones |
| --- | --- | --- |
| Backend y API REST | PHP + CodeIgniter 4 | PHP **8.2.12**; CodeIgniter **4.7.6** al crear el proyecto |
| Frontend | HTML, CSS y JavaScript | Sin framework de SPA previsto |
| Estilos de interfaz | Bootstrap | Archivos compilados **locales**; consultar su encabezado para la versión exacta |
| Base de datos principal | MySQL Server | **8.0.46** |
| Gestor de dependencias PHP | Composer | **2.10.3** |
| Control de versiones | Git | **2.55.0** |
| Repositorio y tareas | GitHub / GitHub CLI | GitHub CLI **2.103.3** |
| Administración de BD | MySQL Workbench | **8.0.47** |
| Servidor local alternativo | Apache (XAMPP) | **2.4.58** en el entorno comprobado |
| Pruebas de API | Postman | Versión pendiente de registrar |
| BD documental auxiliar (opcional) | MongoDB Server / Compass | MongoDB Server **9.0.2** en el entorno comprobado |

**Importante:** las versiones anteriores se recopilaron durante la preparación del proyecto y pueden variar entre Casa e INTECAP. El archivo `composer.lock` es la referencia para las versiones exactas de dependencias PHP instalables con `composer install`. MongoDB es opcional y, de utilizarse, se reservará para funciones auxiliares como auditoría; **MySQL es la fuente principal de datos**.

### Requisitos mínimos del entorno

- Windows con PowerShell, o un sistema compatible con PHP y Composer.
- PHP **8.2 o superior**, con las extensiones que exige CodeIgniter 4, incluidas `intl` y `mbstring`, y las necesarias para MySQL (`mysqli`/`mysqlnd`).
- Extensión PHP `zip` habilitada cuando Composer la necesite para descargar y extraer paquetes.
- Composer y Git disponibles en la terminal.
- MySQL Server 8.0 accesible.
- Un navegador web moderno.

## 3. Repositorio y seguimiento

- **Código:** https://github.com/Weprgt/muebleria-imperial-web
- **Issues:** https://github.com/Weprgt/muebleria-imperial-web/issues
- **Rama principal:** `main`

Las tareas técnicas y las historias del producto se gestionan mediante issues y milestones de GitHub. Se procura registrar en cada issue las decisiones, los comandos ejecutados, las pruebas y los problemas resueltos.

## 4. Estructura inicial del proyecto

```text
muebleria-imperial/
├── app/
│   ├── Config/
│   │   └── Routes.php             # Definición de rutas
│   ├── Controllers/
│   │   └── Inicio.php             # Controlador de inicio
│   ├── Database/
│   │   └── Migrations/            # Migraciones futuras
│   ├── Models/                    # Modelos futuros
│   └── Views/
│       └── frontend/
│           └── inicio.php         # Vista inicial
├── public/
│   ├── index.php                  # Punto de entrada público
│   └── assets/
│       ├── css/
│       │   └── estilos.css
│       └── vendor/
│           └── bootstrap/
│               ├── css/bootstrap.min.css
│               └── js/bootstrap.bundle.min.js
├── writable/                      # Caché, sesiones y registros locales
├── vendor/                        # Dependencias instaladas por Composer
├── composer.json
├── composer.lock
├── env                            # Plantilla de configuración
├── .env                           # Configuración local; NO subir a Git
├── .gitignore
├── spark                          # Herramienta CLI de CodeIgniter
└── README.md
```

Las carpetas vacías de recursos como `public/assets/js` y `public/assets/img` pueden crearse cuando se necesiten; Git no guarda directorios vacíos. La estructura mostrada es **parcial y orientativa**: CodeIgniter genera otros archivos y carpetas que se mantienen en el proyecto.

## 5. Instalación en un equipo nuevo (Casa o INTECAP)

Las instrucciones siguientes asumen PowerShell en Windows y que PHP, Composer, Git y MySQL ya están instalados.

### Paso 1. Obtener el código

```powershell
git clone https://github.com/Weprgt/muebleria-imperial-web.git C:\xampp\htdocs\muebleria-imperial
cd C:\xampp\htdocs\muebleria-imperial
```

Si la carpeta ya existe y es un clon del repositorio, **no vuelvas a clonarla encima**. Revisa antes tus cambios locales:

```powershell
git status
git pull --ff-only origin main
```

No uses `git reset --hard` sin revisar primero si hay trabajo local que necesites conservar.

### Paso 2. Instalar dependencias PHP

```powershell
composer install
```

Se utiliza `composer install`, no `composer update`, para respetar las versiones fijadas en `composer.lock`. La carpeta `vendor/` se genera localmente y no se versiona.

Si Composer informa que faltan extensiones, comprueba el PHP activo y su configuración:

```powershell
php -v
php --ini
php -m
```

Habilita en el `php.ini` correspondiente las extensiones requeridas (por ejemplo, `zip`, `intl` o `mbstring`), abre una terminal nueva y repite `composer install`.

### Paso 3. Preparar la configuración local

Copia la plantilla `env` a `.env` **solo si todavía no existe**:

```powershell
if (-not (Test-Path .env)) { Copy-Item env .env }
```

Edita `.env` con los valores de tu equipo. Ejemplo **sin credenciales reales**:

```dotenv
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = muebleria_imperial
database.default.username = TU_USUARIO_LOCAL
database.default.password = TU_CONTRASENA_LOCAL
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Asegúrate de que las líneas correspondientes no estén comentadas con `#`. La contraseña real se configura **solo en el `.env` local**. No copies claves, contraseñas ni datos privados al README, a los issues ni al repositorio.

### Paso 4. Preparar MySQL

Utiliza **MySQL Server 8.0**, no MariaDB de XAMPP, como motor principal de este proyecto. Comprueba que el servicio de MySQL esté activo y crea la base de datos si todavía no existe:

```sql
CREATE DATABASE IF NOT EXISTS muebleria_imperial
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Crea un usuario de aplicación local con los permisos necesarios, o utiliza uno que ya hayas configurado. Introduce sus credenciales en `.env`; **no utilices credenciales reales en scripts compartidos**.

En el entorno inicial se utilizó un usuario local llamado `muebleria_app`, pero cada equipo puede tener credenciales distintas.

> **Migraciones:** cuando se creen y versionen migraciones para las tablas del negocio, se aplicarán con `php spark migrate`. **No se deben asumir tablas ya implementadas** en el estado actual del proyecto.

### Paso 5. Comprobar la conexión y ejecutar

```powershell
php spark db:table
php spark serve
```

El primer comando permite comprobar el acceso a la base de datos y consultar las tablas existentes. El segundo inicia el servidor de desarrollo integrado.

Abre en el navegador:

**http://localhost:8080/**

La página inicial debe mostrar **«Bienvenido a Mueblería Imperial»**. También carga CSS personalizado y Bootstrap local.

El servidor `php spark serve` **no requiere iniciar Apache de XAMPP**. Si se utiliza Apache con un VirtualHost, el directorio público del sitio debe apuntar a `public/`, nunca a la raíz del proyecto. En el entorno de Casa se configuró el host local `muebleria-imperial.local`; esa dirección requiere configuración local de Apache y del archivo `hosts`, y no está garantizada en otros equipos.

### Paso 6. Verificar Bootstrap local

Comprueba que estas rutas respondan sin error 404:

- http://localhost:8080/assets/vendor/bootstrap/css/bootstrap.min.css
- http://localhost:8080/assets/vendor/bootstrap/js/bootstrap.bundle.min.js

La vista inicial incorpora un botón **«Probar Bootstrap»** que despliega y oculta un mensaje. Esta comprobación permite verificar el CSS y el JavaScript del paquete local.

## 6. Flujo de trabajo con Git

Antes de comenzar en cualquiera de los dos equipos:

```powershell
git status
git pull --ff-only origin main
```

Después de implementar y probar una tarea:

```powershell
git status
git add RUTA_DE_LOS_ARCHIVOS_MODIFICADOS
git diff --cached --stat
git commit -m "TECXX: descripcion breve del cambio"
git push origin main
```

Sustituye los ejemplos por rutas y mensajes reales. Verifica los cambios antes de usar `git add`; evita subir archivos temporales, secretos o artefactos generados.

### Archivos que no deben publicarse

- `.env` y cualquier otro archivo con contraseñas o tokens.
- `vendor/` de Composer (se recupera con `composer install`).
- Archivos temporales, registros y cachés locales que ya estén excluidos por `.gitignore`.

**Excepción deliberada:** se conservan en Git los archivos compilados de Bootstrap ubicados en `public/assets/vendor/bootstrap/`, mediante reglas específicas de `.gitignore`. Esto permite trabajar sin depender de un CDN.

## 7. Modelo de datos previsto

**MySQL** será la base principal. El diseño preliminar contempla las siguientes tablas (sujetas a ajustes antes de sus migraciones):

| Área | Tablas previstas |
| --- | --- |
| Acceso | `roles`, `usuarios` |
| Catálogo | `categorias_muebles`, `muebles` |
| Clientes | `clientes`, `mueble_cliente` |
| Ventas | `pedidos`, `detalle_pedido`, `pagos_pedido`, `entregas_pedido` |
| Reparaciones | `reparaciones`, `fotos_reparacion`, `pagos_reparacion`, `entregas_reparacion` |

MongoDB, si se incorpora, se limitará a datos auxiliares como registros de administración o información adicional de muebles; no duplicará la base transaccional de MySQL.

## 8. Estado del desarrollo y próximos módulos

| Componente | Estado al 04/11/2026 |
| --- | --- |
| Entorno local, repositorio y conexión inicial a MySQL | Preparados y probados |
| Estructura inicial de CI4, ruta y página de inicio | Implementadas |
| CSS personalizado y Bootstrap local | Integrados |
| README específico del proyecto | En elaboración (TEC05) |
| Autenticación y roles | Pendiente |
| Catálogo e inventario | Pendiente |
| Pedidos y ventas | Pendiente |
| Pagos y entregas | Pendiente |
| Reparaciones | Pendiente |
| Dashboard, reportes y pruebas finales | Pendiente |

### Organización por milestones

1. Preparación técnica.
2. Usuarios, catálogo e inventario.
3. Ventas y pedidos.
4. Pagos y entregas.
5. Reparaciones.
6. Dashboard y cierre.

El estado actualizado de cada tarea debe consultarse en los [issues del repositorio](https://github.com/Weprgt/muebleria-imperial-web/issues).

## 9. Solución de problemas frecuentes

| Síntoma | Qué revisar |
| --- | --- |
| `composer install` falla por extensiones | `php --ini`, `php -m` y habilitación de `zip`, `intl`, `mbstring` u otras extensiones requeridas |
| Error de conexión a MySQL | Servicio MySQL80, puerto, nombre de base de datos, usuario y contraseña en `.env` |
| Página inicial 404 | Servidor activo, ruta `/` en `app/Config/Routes.php` y controlador `Inicio` |
| Bootstrap sin estilos | Existencia y URL de `bootstrap.min.css` |
| Botón Bootstrap no responde | Existencia y URL de `bootstrap.bundle.min.js`; consola del navegador |
| Git no agrega Bootstrap | Excepciones de `.gitignore` para `public/assets/vendor/bootstrap/` |
| La aplicación funciona en un equipo y no en otro | Ejecutar `composer install`, revisar `.env`, servicio MySQL y archivos locales |
| Puerto 8080 ocupado | Detener el proceso que lo utiliza o iniciar `php spark serve --port 8081` y ajustar la URL local |

## 10. Seguridad y buenas prácticas

- Usar `.env` local y no versionarlo.
- No almacenar contraseñas, tokens ni credenciales reales en el código o en la documentación pública.
- No exponer `writable/`, `app/`, `vendor/` ni la raíz del proyecto desde el servidor web: el document root debe ser `public/`.
- Mantener `CI_ENVIRONMENT = development` solo en equipos de desarrollo; revisar configuración y seguridad antes de publicar el sistema.
- Validar datos en el backend y controlar permisos por rol al implementar cada módulo.
- Registrar cambios técnicos en issues y mantener el código sincronizado mediante Git.

## 11. Referencias

- [Documentación oficial de CodeIgniter 4](https://codeigniter.com/user_guide/)
- [Documentación de Composer](https://getcomposer.org/doc/)
- [Documentación de MySQL 8.0](https://dev.mysql.com/doc/refman/8.0/en/)
- [Documentación de Bootstrap](https://getbootstrap.com/docs/)
- [Documentación de Git](https://git-scm.com/doc)

---

**Mueblería Imperial** · Proyecto Final Fullstack · Documentación inicial preparada para TEC05.
