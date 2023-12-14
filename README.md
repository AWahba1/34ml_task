
# E-Commerce

## Overview

An e-commerce website to buy and sell products to customers.


## Installation


### Prerequisites

- Docker
- Docker Compose

### Setup

1. **Clone the Repository**

   ```bash
   git clone https://github.com/AWahba1/34ml_task.git
   cd 34ml_task

2. **Environment Configuration**
First, copy the example environment file. Then, open the `.env` file and update it with your environment-specific variables, including the mail configuration.

   ```bash
   cp .env.example .env

3. **Build and Run Docker**
   ```bash
   docker-compose up --build -d

## Usage

### Making API Requests

To interact with the API, you can use `curl` commands. Here's an example of how to make a GET request to retrieve all products:
```
curl -X GET http://localhost:8001/api/products?page=1&per_page=10
```

  ### Query Parameters:

-   `page`: Specifies the page number in the paginated response (Default is 1).
-   `per_page`: Determines the number of items to return per page (Default is 10).
-   `options`: Filters products by options (e.g. "color=red,small" to filter by red color and small size).
-   `average_rating`: Filters products by average rating (e.g. `4.5`).
-   `max_price`: Filters products by maximum price (e.g. `100`).

### Running Tests
```bash
docker exec -it php /bin/sh
php artisan test
```

### Future Enhancements

- Include a Makefile to start up the app and run tests.
- Use proposed database schema [here](/database/database-optimization.md).
