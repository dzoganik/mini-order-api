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
    
    We'll add a short delay to ensure the database service is ready before we continue.
    ```bash
    docker compose up -d --build && sleep 30
    ```

4.  **Run database migrations**
    ```bash
    docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction
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

*   **GET `/api/orders/{id}`** - Retrieve an Order by ID.
    *   **Query Parameters:**
        *   `page` (integer, optional, default: `1`) - The page number you want to display. Must be a positive integer.

    *   **Response:**
        ```json
        {
            "data": [
                {
                    "id": 5,
                    "customerEmail": "user@example.com",
                    "status": "NEW",
                    "totalPrice": 85.5,
                    "createdAt": "2025-08-25T06:51:59+00:00",
                    "orderItems": [
                        {
                            "productName": "Keyboard",
                            "quantity": 1,
                            "unitPrice": 45.5
                        },
                        {
                            "productName": "Mouse",
                            "quantity": 2,
                            "unitPrice": 20
                        }
                    ]
                },
                {
                    "id": 4,
                    "customerEmail": "user@example.com",
                    "status": "NEW",
                    "totalPrice": 85.5,
                    "createdAt": "2025-08-25T06:51:58+00:00",
                    "orderItems": [
                        {
                            "productName": "Keyboard",
                            "quantity": 1,
                            "unitPrice": 45.5
                        },
                        {
                            "productName": "Mouse",
                            "quantity": 2,
                            "unitPrice": 20
                        }
                    ]
                }
            ],
            "meta": {
                "total_items": 2,
                "items_per_page": 10,
                "current_page": 1,
                "total_pages": 1
            }
        }
        ```

*   **GET `/api/orders`** - Retrieves a paginated list of all orders, sorted from newest to oldest.
    *   **Response:**
        ```json
        {
            "id": 1,
            "customerEmail": "user@example.com",
            "status": "NEW",
            "totalPrice": 85.5,
            "createdAt": "2025-08-25T03:19:16+00:00",
            "orderItems": [
                {
                    "productName": "Keyboard",
                    "quantity": 1,
                    "unitPrice": 45.5
                },
                {
                    "productName": "Mouse",
                    "quantity": 2,
                    "unitPrice": 20
                }
            ]
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
