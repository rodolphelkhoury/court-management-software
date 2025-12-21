# Kubernetes Deployment Instructions

## Prerequisites
- Docker Desktop with Kubernetes enabled, OR
- Minikube, OR
- Kind

## Deploy to Kubernetes

1. **Update the Secret**
   Edit `k8s/secret.yaml` and replace `YOUR_APP_KEY_HERE` with your actual APP_KEY (get it from your .env.docker file)

2. **Apply all resources**
   ```powershell
   kubectl apply -f k8s/namespace.yaml
   kubectl apply -f k8s/secret.yaml
   kubectl apply -f k8s/configmap.yaml
   kubectl apply -f k8s/mysql-statefulset.yaml
   kubectl apply -f k8s/backend-deployment.yaml
   kubectl apply -f k8s/backend-service.yaml
   ```

3. **Wait for MySQL to be ready**
   ```powershell
   kubectl wait --for=condition=ready pod -l app=mysql -n court-management --timeout=300s
   ```

4. **Run migrations (one-time setup)**
   ```powershell
   kubectl exec -n court-management deployment/backend -- php artisan migrate --force
   ```

5. **Seed database (optional)**
   ```powershell
   kubectl exec -n court-management deployment/backend -- php artisan db:seed --force
   ```

6. **Access the application**
   - NodePort: http://localhost:30080

## Verify Deployment

```powershell
# Check all resources
kubectl get all -n court-management

# Check pods status
kubectl get pods -n court-management

# Check PersistentVolumeClaims
kubectl get pvc -n court-management

# View backend logs
kubectl logs -n court-management deployment/backend

# View MySQL logs
kubectl logs -n court-management statefulset/mysql

# Test readiness probe
kubectl describe pod -n court-management -l app=backend
```

## Cleanup

```powershell
kubectl delete namespace court-management
```
