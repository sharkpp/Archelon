## memo

    php oil g model user --soft-delete --mysql-timestamp
    php oil g model account user_id:int connector_id:int --soft-delete --mysql-timestamp
    php oil g model connector enable:int name:text --soft-delete --mysql-timestamp
    php oil g model config file:text config:text --mysql-timestamp
    php oil g model dneo_user account_id:int user_name:text salt:text password:text send_email:int --soft-delete --mysql-timestamp
    php oil g controller index     index
    php oil g controller account   index new edit delete
    php oil g controller user      index new edit delete
    php oil g controller module    index edit delete update
    php oil r migrate --packages=auth
    php oil r migrate

    http://192.168.11.4:8086/

