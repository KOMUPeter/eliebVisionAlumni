# Elieb Vision Alumni

## General Description:
The application aims to facilitate communication and management within Elieb Vision Alumni. It allows users to register, maintain their profiles, subscribe to monthly services, participate in events, and communicate via group and private messaging.
## Specifications / Cahier de Charge:

### Functionalities:
1. **User Management:**
   - Registration with basic information (prenom, nom, email, phone_number, unique_id).
   - Authentication and password management.
   - Role-based access control (Admin, Treasurer, Vice President, Secretary, Senior Member, Regular User).
2. **Subscription Management:**
   - Monthly subscription tracking for members.
   - Ability for Treasurer to update subscription status.
3. **Event Management:**
   - Creation, viewing, and participation in events.
4. **Messaging:**
   - Group messaging for all members.
   - Private messaging between individual members.
5. **Real-time Communication:**
   - Integration of WebSockets for real-time messaging.
6. **Monthly payout tracking for members:**
   - Implement functionality to track monthly payouts for each member.
   - Ability for Treasurer to update payout status: Allow the Treasurer role to update payout details.
   - Viewing payout details: Regular users can view their own payout details.

### Security:
1. **Authentication:**
   - Secure user authentication and session management.
   - Password hashing and salting.
2. **Authorization:**
   - Role-based access control for different functionalities.
   - Granular permissions for each role.
3. **Data Protection:**
   - Encryption of sensitive data.
   - Protection against SQL injection and other security vulnerabilities.
4. **Compliance:**
   - GDPR compliance for handling user data.
   - Secure transmission of data over HTTPS.

### Technical Requirements:
1. **Symfony Framework:**
   - Latest stable version.
   - Utilize Symfony Flex for project management.
2. **React.js with Vite:**
   - Frontend developed using React.js with Vite for fast development.
   - Responsive design for optimal user experience across devices.
3. **Database:**
   - MySQL or PostgreSQL for data storage.
4. **Real-time Messaging:**
   - Integration of WebSockets using Symfony's Mercure component or alternative solutions like Pusher or Socket.io.
5. **Deployment:**
   - Deployment on a secure server with HTTPS support.
   - Utilize Docker for containerization if needed.

## Tasks:

### Symfony Application Setup:
1. Set up Symfony project.
2. Configure database connection.
3. Install necessary Symfony bundles.

### Entity Creation:
1. Create entities for users, roles, permissions, events, subscriptions, and messages.
2. Define relationships between entities.

### User Roles and Permissions:
1. Implement role-based access control (RBAC) using Symfony's security component.
2. Define access control rules based on user roles.

### Chat Functionality:
1. Implement chat functionality for group and private messaging.
2. Integrate real-time messaging using WebSockets for instant messaging.

### Subscription Management:
1. Create functionality for managing user subscriptions.
2. Implement monthly subscription tracking.

## Entity List:

### User:
- id
- firstNames
- surname
- email
- phone_number
- registration_date
- isSubscribed
- password
- role (Many-to-One relationship with Role entity)
- nextOfKin (One-to-One relationship with User entity)
- payouts (One-to-Many relationship with Payout entity)

### Role:
- id
- name

### Event:
- id
- title
- description
- date

### Message:
- id
- sender_id (Many-to-One relationship with User entity)
- receiver_id (Many-to-One relationship with User entity)
- content
- timestamp

### Payout:
- id
- user_id (Many-to-One relationship with User entity)
- payout_date
- amount

### NextOfKins:
- id
- firstNames
- surname
- email
- phone_number

