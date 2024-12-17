# Laravel Search App

A Laravel application with MySQL full-text search capabilities, containerized with Docker and deployed to Kubernetes.

## Local Development with Docker

### Requirements

-   Docker
-   Docker Compose
-   Git
-   Kubernetes

### Setup Steps

1. Create `.env` file:

```bash
cp .env.example .env
```

2. Start Docker containers:

```bash
docker-compose up -d
```

3. Install dependencies:

```bash
docker-compose exec app composer install
```

4. Generate application key:

```bash
docker-compose exec app php artisan key:generate
```

7. Run migrations and seed the database:

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed --class=ProductTestSeeder
```

Your application should now be running at http://localhost:8000

## Running Tests

Run the test suite:

```bash
docker-compose exec app php artisan test
```

## Kubernetes Deployment

### Prerequisites

-   Kubernetes cluster
-   kubectl CLI
-   Docker registry access (we use GitHub Container Registry)

### Deployment Steps

### Apply Kubernetes configurations:

```bash
# Apply secrets first
kubectl apply -f kubernetes/secrets.yml

# Deploy MySQL
kubectl apply -f kubernetes/mysql.yml

# Deploy Laravel application
kubectl apply -f kubernetes/deployment.yml
```

### Verify deployment:

```bash
# Check pods status
kubectl get pods

# Check services
kubectl get services
```

### Project Structure

```
├─- .github/
│   └── workflows/        # CI/CD pipeline configs
│       └── main.yml
├── kubernetes/          # Kubernetes manifests
│   ├── deployment.yml   # Laravel app deployment
│   ├── mysql.yml       # MySQL database
│   └── secrets.yml     # Sensitive configurations
├── tests/              # Test files
|
└── Other files

```

## CI/CD Pipeline

The GitHub Actions pipeline:

1. Runs tests
2. Builds Docker image
3. Pushes to GitHub Container Registry
4. Deploys to Kubernetes cluster

### Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f
```
