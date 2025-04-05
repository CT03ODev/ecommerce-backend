1.  Install composer packages
    
    ```plaintext
    composer install
    ```

---

2.  Install node packages
    
    ```plaintext
    npm install
    ```
    
---

3.  Config your database connection in .env (see: .env.example)
    
    ```plaintext
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=root
    DB_PASSWORD=
    ```
    
---

4.  Run migration
    
    ```plaintext
    php artisan migrate
    ```

---
    
5.  Run init seeder
    
    ```plaintext
    php artisan db:seed
    ```
    
---

6.  Create the symbolic link
    
    ```plaintext
    php artisan storage:link
    ```
    
---

7.  Running an application for the first time
    
    ```plaintext
    php artisan serve
    ```
    
    ```plaintext
    npm run dev
    ```
    
---

Important: Make sure to update the application path in the `APP_URL` variable within the .env file to prevent any issues with resource uploads.