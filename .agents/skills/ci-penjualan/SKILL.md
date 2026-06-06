```markdown
# ci-penjualan Development Patterns

> Auto-generated skill from repository analysis

## Overview
This skill guide documents the core development patterns and workflows used in the `ci-penjualan` repository, a JavaScript backend project built with Express. The guide covers coding conventions, step-by-step workflows for common feature additions (such as real-time notifications and UI-backend integrations), and testing practices. It is designed to help contributors quickly understand and follow the project's established practices.

## Coding Conventions

**File Naming**
- Use `camelCase` for file names.
  - Example: `userController.js`, `orderRoutes.js`

**Import Style**
- Use relative imports for modules within the project.
  ```js
  // Good
  const userService = require('../services/userService');
  ```

**Export Style**
- Use named exports.
  ```js
  // userController.js
  function getUser(req, res) { ... }
  function createUser(req, res) { ... }
  module.exports = { getUser, createUser };
  ```

**Commit Messages**
- Follow [Conventional Commits](https://www.conventionalcommits.org/) with these prefixes:
  - `feat`: New features
  - `fix`: Bug fixes
  - `refactor`: Code refactoring
- Example:
  ```
  feat: add websocket notification dropdown to navbar
  fix: correct notification badge count logic
  refactor: extract notification logic to separate module
  ```

## Workflows

### Implement Real-Time Notifications (WebSocket)
**Trigger:** When adding or enhancing real-time notification features (e.g., replacing polling, adding notification dropdowns, improving notification UX).
**Command:** `/add-realtime-notification`

1. **Client Setup:**  
   Add or update Socket.IO client initialization in `public/libraries/js/script.js`.
   ```js
   // script.js
   const socket = io();
   socket.on('notification', (data) => {
     // Handle notification
   });
   ```
2. **Server Setup:**  
   Update or create the server-side WebSocket handler, e.g., `public/my-component/Socket.js`.
   ```js
   // Socket.js
   io.on('connection', (socket) => {
     socket.emit('notification', { message: 'New sale!' });
   });
   ```
3. **UI Integration:**  
   Modify layout or modal views to inject WebSocket-related variables or UI elements, such as in `app/Views/layouts/app.php` or `app/Views/user/modal.php`.
   ```php
   <!-- app.php -->
   <script src="/socket.io/socket.io.js"></script>
   <script src="/libraries/js/script.js"></script>
   ```
4. **Notification Display:**  
   Update notification display logic, e.g., using `Swal.fire` or a dropdown in `app/Views/layouts/navbar.php`.
   ```php
   <!-- navbar.php -->
   <div id="notification-dropdown"></div>
   ```
5. **Configuration:**  
   Update configuration files if necessary, such as `app/Config/Api.php`.
6. **Database Migrations:**  
   Add or update notification-related database migrations in `app/Database/Migrations/`.

### Feature UI-Backend Integration
**Trigger:** When adding a new entity, feature, or test scenario that requires both backend and frontend changes.
**Command:** `/new-feature-ui-backend`

1. **Backend Controller:**  
   Add or update the backend controller in `app/Controllers/`.
   ```js
   // app/Controllers/productController.js
   function addProduct(req, res) { ... }
   module.exports = { addProduct };
   ```
2. **Routing Configuration:**  
   Update routing configuration in `app/Config/Routes.php`.
   ```php
   // Routes.php
   $routes->post('product/add', 'ProductController::addProduct');
   ```
3. **Frontend Views:**  
   Create or update frontend view files, such as `app/Views/product/index.php` and `app/Views/product/modal.php`.
   ```php
   <!-- index.php -->
   <button data-toggle="modal" data-target="#addProductModal">Add Product</button>
   ```
4. **Debug/Test Output (Optional):**  
   Optionally update debug or test output, e.g., `writable/debugbar/index.html`.

## Testing Patterns

- **Test File Pattern:**  
  Test files follow the `*.test.*` naming convention, e.g., `userController.test.js`.
- **Testing Framework:**  
  The specific testing framework is not detected; check existing test files for framework usage.
- **Example Test File:**
  ```js
  // userController.test.js
  const { getUser } = require('../controllers/userController');
  test('should return user data', () => {
    // test implementation
  });
  ```

## Commands

| Command                   | Purpose                                               |
|---------------------------|------------------------------------------------------|
| /add-realtime-notification| Implement or update real-time notification features  |
| /new-feature-ui-backend   | Add a new feature with backend and frontend changes  |
```
