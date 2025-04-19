# Wallet System - SOAP API

Servicio SOAP implementado con Symfony para el sistema de billetera electrónica.

## Despliegue

1. Configurar variables de entorno para producción
2. Optimizar autoloader:
```bash
composer dump-autoload --optimize --no-dev --classmap-authoritative
```

3. Limpiar y calentar caché:
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

## Monitoreo

- Los logs de aplicación están en `var/log/`
- Monitoreo de performance con Symfony Profiler en desarrollo
- Integración con servicios de monitoreo externos

## Contribuir

1. Crear una rama para la nueva funcionalidad
2. Implementar cambios siguiendo los estándares PSR
3. Escribir/actualizar tests
4. Crear Pull Request


## Contacto

JC Developer..!
