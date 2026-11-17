# Mueblería Imperial

**Proyecto Final Fullstack — INTECAP · Aplicación web · Actualizado: 16 de noviembre de 2026**

Mueblería Imperial es una aplicación web para gestionar el catálogo de una mueblería, cuentas de clientes y empleados, carrito de compras, pedidos, pagos y anticipos, entregas, cancelaciones, reembolsos de pedidos cancelados y reparaciones de muebles. Incluye paneles por rol, reportes, una API REST de catálogo y auditoría auxiliar en MongoDB.

> **Estado de la versión Alpha (16/11/2026):** los principales flujos están implementados y se han realizado pruebas manuales parciales. Esto **no equivale** a una auditoría completa de seguridad, concurrencia ni pruebas automatizadas. Las devoluciones físicas de productos ya entregados no deben confundirse con los reembolsos de pedidos cancelados.

## 1. Alcance

El proyecto es **exclusivamente web**, sin aplicación Android. El catálogo y las compras están orientados al cliente por internet; los empleados disponen de funciones administrativas y operativas.

**Roles:**
- **Administrador:** gestión de muebles, categorías, empleados y operaciones autorizadas.
- **Ventas:** verificación de pagos, pedidos, entregas, cancelaciones, reembolsos y reportes.
- **Taller:** evaluación, presupuesto, ejecución y entrega de reparaciones.
- **Cliente:** registro, autenticación, carrito, pedidos, pagos, cancelaciones y solicitudes de reparación.

## 2. Tecnologías

| Área | Tecnología |
| --- | --- |
| Backend y API REST | PHP 8.2+ y CodeIgniter 4 (versión de referencia: 4.7.6) |
| Frontend | HTML, CSS, JavaScript y Bootstrap |
| Base de datos transaccional | MySQL 8 |
| Auditoría auxiliar | MongoDB y biblioteca PHP `mongodb/mongodb` |
| Dependencias | Composer (`composer.lock` fija versiones) |
| Control de versiones | Git y GitHub |
| Entorno local | Windows, PowerShell, PHP CLI o Apache/XAMPP |

Las versiones exactas de herramientas pueden variar entre los equipos de Casa e INTECAP. **MySQL es la fuente de verdad del negocio**; MongoDB no sustituye las transacciones relacionales.

## 3. Funcionalidades implementadas

| Módulo | Alcance disponible | Consideraciones |
| --- | --- | --- |
| Autenticación y roles | Registro de clientes, inicio y cierre de sesión, filtros de acceso, CSRF, hash de contraseñas y limitación de intentos | Pendiente una revisión integral de seguridad y permisos |
| Catálogo y administración | Catálogo público, categorías, CRUD de muebles, imágenes y gestión de empleados | Faltan pruebas exhaustivas de inventario y cargas |
| Carrito y pedidos | Agregar, actualizar y quitar artículos; calcular totales; crear pedidos y consultar su estado | Faltan pruebas de concurrencia y regresión |
| Pagos de pedidos | Registro de anticipos y saldos; aprobación o rechazo por Ventas/Administrador | No es una pasarela bancaria automática |
| Entregas y cancelaciones | Preparación, entrega, cancelación directa o solicitada y aprobación/rechazo | Faltan pruebas integrales de todos los casos límite |
| Reembolsos | Solicitudes, rechazo y procesamiento administrativo de reembolsos de pedidos cancelados, incluidos parciales | Procesar no ejecuta transferencias bancarias |
| Reparaciones | Solicitud, evaluación, presupuesto, aceptación, anticipos, verificación, trabajo, pago de saldo y entrega | Requiere regresión extremo a extremo |
| Reportes y paneles | Paneles por rol y consultas de reportes | Validación de cobertura y exportaciones según alcance |
| API REST | Consulta de muebles, detalle y categorías; búsqueda, filtros, ordenamiento y paginación de muebles | API de consulta, no una API completa de todas las operaciones |
| Auditoría MongoDB | Registro auxiliar de eventos administrativos y de pedidos/cancelaciones | Pendientes lectura administrativa, retención y pruebas negativas |

### Funcionalidades pendientes o fuera del alcance Alpha comprobado

- **Devoluciones físicas totales/parciales de pedidos ya entregados**, con inspección y eventual reingreso al inventario.
- Pruebas automatizadas, de concurrencia, permisos exhaustivos, CSRF adversarial y regresión móvil/escritorio.
- Endurecimiento de seguridad, políticas de retención MongoDB y validación completa de restauración.
- Revisión final de criterios originales de cada historia de usuario y evidencias de entrega.

Consulta los [issues](https://github.com/Weprgt/muebleria-imperial-web/issues) para el seguimiento detallado. Un issue cerrado no sustituye una prueba funcional.

## 4. Arquitectura

Se utiliza el patrón **MVC** de CodeIgniter 4:
- **Rutas:** `app/Config/Routes.php` enlaza URL, método HTTP, controlador y filtros de acceso.
- **Controladores:** `app/Controllers/` gestiona las peticiones y reglas de flujo.
- **Modelos:** `app/Models/` interactúa con MySQL.
- **Vistas:** `app/Views/` presenta la interfaz.
- **Migraciones y seeders:** `app/Database/` define y prepara estructuras y datos iniciales.
- **Servicios:** `app/Services/` incluye integración auxiliar, como auditoría MongoDB.
- **Archivos públicos:** `public/` es la única raíz que debe exponerse en el servidor.

## 5. Instalación local

**Requisitos:** PHP compatible con CodeIgniter 4, Composer, MySQL Server 8, Git y extensiones PHP requeridas (`intl`, `mbstring`, `mysqli`; habilitar `zip` y `mongodb` cuando corresponda).

```powershell
git clone https://github.com/Weprgt/muebleria-imperial-web.git C:\xampp\htdocs\muebleria-imperial
cd C:\xampp\htdocs\muebleria-imperial
composer install
if (-not (Test-Path .env)) { Copy-Item env .env }
```

Edita el archivo **`.env` local**, sin subirlo al repositorio. Ejemplo sin credenciales reales:

```dotenv
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = 127.0.0.1
database.default.database = muebleria_imperial
database.default.username = TU_USUARIO_LOCAL
database.default.password = TU_CONTRASENA_LOCAL
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Crea la base de datos MySQL y **elige uno de los métodos de preparación**, según el entorno y los datos disponibles:

1. **Instalación desde cero:** ejecutar las migraciones existentes y los seeders necesarios, revisando antes sus requisitos y dependencias.
2. **Restauración de demostración:** importar el respaldo SQL preparado para la entrega; **no ejecutar automáticamente migraciones o seeders encima de datos restaurados sin revisar su estado**.

Comandos útiles:

```powershell
php spark migrate:status
php spark migrate
php spark serve
```

`php spark migrate` corresponde al método de instalación desde cero. La URL local por defecto es `http://localhost:8080/`; si utilizas el VirtualHost `muebleria-imperial.local`, configura `app.baseURL`, Apache y el archivo `hosts` de forma coherente. Las discrepancias entre host y URL pueden provocar errores CSRF o de sesión.

**No se publican contraseñas de usuarios de demostración.** Configura o restaura cuentas mediante los procedimientos seguros del entorno.

## 6. Rutas representativas

| Método | Ruta | Uso |
| --- | --- | --- |
| GET | `/catalogo` | Catálogo público |
| GET/POST | `/registro` | Registro de clientes |
| GET/POST | `/login` | Acceso |
| GET | `/dashboard` | Panel protegido |
| GET | `/carrito` | Carrito |
| GET | `/mis-pedidos` | Pedidos del cliente |
| GET | `/mis-reparaciones` | Reparaciones del cliente |
| GET | `/reportes` | Reportes para roles autorizados |
| GET | `/api/muebles` | Listado de muebles en JSON |
| GET | `/api/muebles/{id}` | Detalle de mueble en JSON |
| GET | `/api/categorias` | Categorías en JSON |

Las rutas de operaciones que modifican datos utilizan **POST** y filtros de roles según corresponda. Consulta `app/Config/Routes.php` para la relación completa y actualizada.

## 7. API REST

La API expone consultas de catálogo en formato JSON. El listado de muebles contempla búsqueda, filtrado por categoría/precio, ordenamiento y paginación. Se realizaron pruebas manuales de respuestas positivas y negativas; no se declara cobertura automatizada.

## 8. Bases de datos y respaldos

- **MySQL 8:** almacena usuarios, catálogo, pedidos, pagos, entregas, reparaciones y demás datos transaccionales.
- **MongoDB:** almacena eventos de auditoría auxiliar en la colección `muebleria_imperial_logs.auditoria_eventos`.

Los respaldos de MySQL y MongoDB, así como el código fuente empaquetado y la documentación de instalación, forman parte de la carpeta de entrega del proyecto. No incluyas secretos, credenciales ni datos personales reales en repositorios públicos.

## 9. Pruebas y estado académico

La versión Alpha cuenta con comprobaciones manuales de autenticación, permisos parciales, carrito, pagos, cancelaciones, reembolsos, API y auditoría, además de pruebas reportadas por el responsable del proyecto para otros módulos. **No todas fueron ejecutadas de manera independiente ni existe aún una batería automatizada integral.**

La documentación de entrega distingue entre funcionalidades demostradas, pruebas manuales, evidencia registrada y mejoras pendientes.

## 10. Git y contribución

```powershell
git status
git pull --ff-only origin main
# Después de modificar y probar:
git add RUTA_DEL_ARCHIVO
git diff --cached --stat
git commit -m "descripcion del cambio"
git push origin main
```

No versiones `.env`, dependencias generadas (`vendor/`), cachés, registros o respaldos con información sensible. El paquete ZIP creado con `git archive` incluye únicamente los archivos versionados en el commit seleccionado.

## 11. Referencias

- [Repositorio GitHub](https://github.com/Weprgt/muebleria-imperial-web)
- [Issues y seguimiento](https://github.com/Weprgt/muebleria-imperial-web/issues)
- [CodeIgniter 4](https://codeigniter.com/user_guide/)
- [Composer](https://getcomposer.org/doc/)
- [MySQL 8](https://dev.mysql.com/doc/refman/8.0/en/)
- [MongoDB](https://www.mongodb.com/docs/)

---

**Mueblería Imperial · Proyecto Final Fullstack INTECAP · Estado documentado al 16/11/2026.**
