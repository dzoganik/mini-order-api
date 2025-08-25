# Order API
A sample project built with Symfony for creating orders via a REST API.

## Used Technologies
- Symfony 7.3
- PHP 8.4
- MariaDb 11.7
- Nginx
- Docker
- Adminer
- PHPUnit

## Prerequisites
*   Docker (recommended 28.2.2)
*   Docker Compose (recommended v2.36.2)
*   GIT

## Installation and Setup
1.  **Clone the repository**
    ```bash
    git clone https://github.com/dzoganik/mini-order-api.git
    ```
    
2.  **Navigate into the project directory**
    ```bash
    cd mini-order-api
    ```

3.  **Run with Docker Compose**
    ```bash
    docker compose up -d --build
    ```

## API Endpoints

### Orders
*   **POST `/api/orders`** - Creates a new order.
    *   **Request:**
        ```bash
        Host: localhost
        Content-Type: application/json

        {
            "customer_email": "user@example.com",
            "items": [
                {
                    "product_name": "Keyboard",
                    "unit_price": 45.50, "quantity": 1
                },
                {
                    "product_name": "Mouse",
                     "unit_price": 20.00,
                     "quantity": 2
                }
            ]
        }
        ```
    *   **Response:**
        ```json
        {
            "order ID": 1,
            "status": "NEW"
        }
        ```

## Testing
```bash
docker compose exec app bin/phpunit
```

## Code Style
This project follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.

Code quality is checked using [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer):

```bash
docker compose exec app vendor/bin/phpcs --standard=PSR12 src migrations tests
```
