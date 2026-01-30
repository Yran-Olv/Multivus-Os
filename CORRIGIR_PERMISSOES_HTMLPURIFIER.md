# 🔧 Corrigir Permissões do HTMLPurifier

## Problema
O HTMLPurifier precisa de permissão de escrita no diretório de cache:
```
/var/www/html/application/vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer
```

## Solução

### Opção 1: Via Docker (Recomendado)

Se você está usando Docker, execute:

```bash
cd docker
docker-compose exec php-fpm chmod -R 777 /var/www/html/application/vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache
```

Ou crie o diretório e dê permissão:

```bash
docker-compose exec php-fpm mkdir -p /var/www/html/application/vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer
docker-compose exec php-fpm chmod -R 777 /var/www/html/application/vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache
```

### Opção 2: Via Terminal Local

Se você tem acesso ao servidor:

```bash
cd /home/yran/Área\ de\ trabalho/Multivus-Os
chmod -R 777 application/vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache
```

### Opção 3: Desabilitar Cache do HTMLPurifier (Temporário)

Se não conseguir alterar permissões, você pode desabilitar o cache do HTMLPurifier editando o helper:

Edite `application/helpers/general_helper.php` e modifique a função `printSafeHtml` para desabilitar o cache.

---

## Verificação

Após corrigir as permissões, tente acessar novamente:
- `/index.php/os/visualizar/2`
- O erro não deve mais aparecer

---

**Nota:** Este é um aviso (warning), não um erro fatal. O sistema deve continuar funcionando, mas pode ser mais lento sem o cache do HTMLPurifier.
