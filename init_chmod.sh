cd /var/www/fsshLunchSystem
chown -R www-data:www-data ./
find ./ -type d -exec chmod 775 {} \;
find ./ -type f -exec chmod 664 {} \;
chmod 400 storage/oauth-public.key
chmod 400 storage/oauth-private.key
chmod 775 init_chmod.sh
