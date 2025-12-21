# Court Management Software

## Overview

Court Management Software is a Laravel-based web application for managing sports court reservations, including basketball, tennis, and padel courts. The application provides comprehensive features for managing complexes, courts, reservations, and customer relationships.

### Key Features
- Multi-sport court management (Basketball, Tennis, Padel, Volleyball, etc.)
- Complex and section organization
- Court type and surface type management
- Customer reservation system
- Invoice generation
- Company and customer relationship management
- Real-time availability tracking

### Technology Stack
- **Backend**: Laravel 12.31.1, PHP 8.2
- **Frontend**: Inertia.js, Vue.js, TailwindCSS
- **Database**: MySQL 8.0
- **Containerization**: Docker, Docker Compose
- **Orchestration**: Kubernetes
- **CI/CD**: GitHub Actions
- **Web Server**: Nginx + PHP-FPM + Supervisor

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         GitHub Repository                        │
│                  (court-management-software)                     │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            │ Push to Pipelining branch
                            ↓
┌─────────────────────────────────────────────────────────────────┐
│                      GitHub Actions CI/CD                        │
├─────────────────────────────────────────────────────────────────┤
│  Stage 1: Code Quality (Pint)                                   │
│  Stage 2: Unit Tests                                             │
│  Stage 3: Feature Tests                                          │
│  Stage 4: Build Docker Image                                     │
│  Stage 5: Push to Docker Hub                                     │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            │ Docker Image
                            ↓
                ┌───────────────────────┐
                │   Docker Hub Registry │
                │  rodolphelkhoury/     │
                │  court-management-    │
                │  software:latest      │
                └───────────────────────┘
                            │
        ┌───────────────────┴───────────────────┐
        │                                       │
        ↓                                       ↓
┌──────────────────┐                  ┌──────────────────┐
│ Docker Compose   │                  │   Kubernetes     │
│  (Development)   │                  │   (Production)   │
├──────────────────┤                  ├──────────────────┤
│ • Backend        │                  │ • Namespace      │
│ • MySQL DB       │                  │ • Deployment     │
│ • Volumes        │                  │ • StatefulSet    │
│ • Network        │                  │ • Services       │
│                  │                  │ • ConfigMap      │
│ Port: 8000       │                  │ • Secret         │
└──────────────────┘                  │ • PVC            │
                                      │                  │
                                      │ Port: 30080      │
                                      └──────────────────┘

Application Architecture:
┌─────────────────────────────────────────────────────────────────┐
│                          Client Browser                          │
└───────────────────────────┬─────────────────────────────────────┘
                            │ HTTP
                            ↓
┌─────────────────────────────────────────────────────────────────┐
│                    Nginx (Port 80/8000/30080)                   │
└───────────────────────────┬─────────────────────────────────────┘
                            │ FastCGI
                            ↓
┌─────────────────────────────────────────────────────────────────┐
│                       PHP-FPM (Laravel 12)                       │
├─────────────────────────────────────────────────────────────────┤
│  Controllers → Services → Models → Database                     │
│  • CompanyController     • Company                              │
│  • CourtController       • Court                                │
│  • ReservationController • Reservation                          │
│  • CustomerController    • Customer                             │
└───────────────────────────┬─────────────────────────────────────┘
                            │ PDO/MySQL
                            ↓
┌─────────────────────────────────────────────────────────────────┐
│                      MySQL 8.0 Database                          │
├─────────────────────────────────────────────────────────────────┤
│  Tables: companies, users, customers, complexes, courts,        │
│          reservations, invoices, court_types, surface_types     │
└─────────────────────────────────────────────────────────────────┘
```

---

## Docker Compose Instructions

### Prerequisites
- Docker Desktop installed
- Docker Compose installed

### Setup and Run

1. **Clone the repository**
   ```bash
   git clone https://github.com/rodolphelkhoury/court-management-software.git
   cd court-management-software
   ```

2. **Create environment file**
   ```bash
   cp .env.docker.example .env.docker
   ```
   
   Update `.env.docker` with your configuration (especially `APP_KEY`)

3. **Build and start services**
   ```bash
   docker-compose --env-file .env.docker up -d --build
   ```

4. **Run migrations**
   ```bash
   docker exec court-management-backend php artisan migrate --force
   ```

5. **Seed database (optional)**
   ```bash
   docker exec court-management-backend php artisan db:seed --force
   ```

6. **Access the application**
   - URL: http://localhost:8000

### Docker Compose Services

- **backend**: Laravel application (Nginx + PHP-FPM + Supervisor)
- **database**: MySQL 8.0
- **Volumes**: 
  - `db_data`: MySQL persistent storage
  - `storage_data`: Laravel storage
  - `logs_data`: Application logs
- **Network**: `court-network` (bridge)

### Useful Commands

```bash
# View logs
docker-compose --env-file .env.docker logs -f backend

# Stop services
docker-compose --env-file .env.docker down

# Rebuild services
docker-compose --env-file .env.docker up -d --build

# Execute artisan commands
docker exec court-management-backend php artisan [command]
```

---

## CI/CD Instructions

### GitHub Actions Pipeline

The CI/CD pipeline consists of 5 stages:

1. **Code Quality**: Runs Laravel Pint for code style checking
2. **Unit Tests**: Executes unit tests with Pest
3. **Feature Tests**: Runs feature/integration tests
4. **Build**: Creates Docker image with Buildx and layer caching
5. **Push**: Pushes image to Docker Hub registry

### Setup

1. **Configure GitHub Secrets**
   
   Go to: `Settings → Secrets and variables → Actions → New repository secret`
   
   Required secrets:
   - `DOCKER_USERNAME`: Your Docker Hub username
   - `DOCKER_PASSWORD`: Docker Hub access token (with Read, Write, Delete permissions)
   - `CODECOV_TOKEN`: (Optional) Codecov token for coverage reports

2. **Create Docker Hub Access Token**
   - Visit https://hub.docker.com/settings/security
   - Click "New Access Token"
   - Name: `github-actions`
   - Permissions: **Read, Write, Delete**
   - Copy the token and add to GitHub secrets

3. **Trigger Pipeline**
   ```bash
   git add .
   git commit -m "Your commit message"
   git push origin Pipelining
   ```

### Pipeline Configuration

The pipeline runs on:
- Push to `Pipelining` branch
- Pull requests to `Pipelining` branch

### Viewing Pipeline Results

- Go to your repository on GitHub
- Click **Actions** tab
- View workflow runs and their status

---

## Kubernetes Deployment Instructions

### Prerequisites
- Docker Desktop with Kubernetes enabled, OR
- Minikube, OR
- Kind cluster

### Deployment Steps

1. **Navigate to project directory**
   ```bash
   cd court-management-software
   ```

2. **Apply Kubernetes resources**
   ```bash
   kubectl apply -f k8s/namespace.yaml
   kubectl apply -f k8s/secret.yaml
   kubectl apply -f k8s/configmap.yaml
   kubectl apply -f k8s/mysql-statefulset.yaml
   kubectl apply -f k8s/backend-deployment.yaml
   kubectl apply -f k8s/backend-service.yaml
   ```

3. **Wait for MySQL to be ready**
   ```bash
   kubectl wait --for=condition=ready pod -l app=mysql -n court-management --timeout=300s
   ```

4. **Run database migrations**
   ```bash
   kubectl exec -n court-management deployment/backend -- php artisan migrate --force
   ```

5. **Seed database (optional)**
   ```bash
   kubectl exec -n court-management deployment/backend -- php artisan db:seed --force
   ```

6. **Access the application**
   - URL: http://localhost:30080

### Kubernetes Resources

- **Namespace**: `court-management`
- **Deployment**: `backend` (Laravel application)
- **StatefulSet**: `mysql` (MySQL database)
- **Services**:
  - `backend-service` (NodePort: 30080)
  - `mysql-service` (Headless service for StatefulSet)
- **ConfigMap**: `app-config` (non-sensitive configuration)
- **Secret**: `app-secret` (APP_KEY, DB_PASSWORD)
- **PersistentVolumeClaim**: `mysql-storage-mysql-0` (1Gi)

### Probes Configuration

**Liveness Probe**:
- Path: `/`
- Initial Delay: 30s
- Period: 10s
- Timeout: 5s
- Failure Threshold: 3

**Readiness Probe**:
- Path: `/`
- Initial Delay: 10s
- Period: 5s
- Timeout: 3s
- Failure Threshold: 3

### Useful Kubernetes Commands

```bash
# View all resources
kubectl get all -n court-management

# Check pods
kubectl get pods -n court-management

# View logs
kubectl logs -n court-management deployment/backend

# Check PersistentVolumeClaims
kubectl get pvc -n court-management

# Describe pod (includes probe status)
kubectl describe pod -n court-management -l app=backend

# Delete all resources
kubectl delete namespace court-management
```

---

## Database Design Justification

### Database Choice: MySQL 8.0

**Rationale**:
- **ACID Compliance**: Ensures data integrity for financial transactions (invoices, reservations)
- **Relational Structure**: Court management requires complex relationships between entities
- **Proven Reliability**: Production-ready with extensive Laravel support
- **Performance**: Optimized for read-heavy operations (court availability queries)

### Schema Design

#### Core Tables

1. **companies**: Multi-tenancy support for different court management businesses
2. **users**: Authentication and authorization with company association
3. **customers**: Customer information with company relationship
4. **complexes**: Physical locations containing multiple courts
5. **courts**: Individual court entities with type, surface, pricing
6. **reservations**: Booking records with time slots and customer info
7. **invoices**: Financial records linked to reservations

#### Lookup Tables

8. **court_types**: Basketball, Tennis, Padel, Volleyball (enum-backed)
9. **surface_types**: Hard, Clay, Grass, Synthetic, Parquet (enum-backed)
10. **court_type_surface_type**: Many-to-many relationship for valid combinations

### Key Design Decisions

**1. Normalization**
- 3NF (Third Normal Form) to eliminate data redundancy
- Separate tables for types/categories with foreign key constraints
- Junction table for many-to-many relationships

**2. Indexing Strategy**
- Primary keys on all tables (auto-increment)
- Foreign key indexes for join performance
- Unique constraints on email fields
- Composite indexes for frequently queried combinations (e.g., court + date)

**3. Data Integrity**
- Foreign key constraints with `ON DELETE CASCADE` where appropriate
- `RESTRICT` on critical relationships (e.g., company deletion)
- NOT NULL constraints on required fields
- Email validation at database level

**4. Soft Deletes**
- Implemented on user-facing tables for audit trail
- `deleted_at` timestamp for recovery capability

**5. Timestamps**
- `created_at` and `updated_at` on all tables
- Automatic tracking via Laravel ORM

**6. JSON Columns**
- Used sparingly for flexible metadata (e.g., court amenities)
- Avoids over-normalization for non-queryable data

### Scalability Considerations

- **Sharding Ready**: Company-based partitioning possible for horizontal scaling
- **Read Replicas**: Separation of read/write queries supported
- **Connection Pooling**: Configured in Laravel for efficient connection management
- **Query Optimization**: Eager loading relationships to prevent N+1 queries

### Persistent Storage in Kubernetes

- **StatefulSet** for MySQL ensures:
  - Stable network identity
  - Ordered deployment and scaling
  - Persistent storage via PVC
- **PersistentVolumeClaim** (1Gi):
  - Data survives pod restarts
  - Backed by host path in Docker Desktop
  - Can be migrated to cloud storage (EBS, Persistent Disk)

---

## License

This project is licensed under the MIT License.

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
