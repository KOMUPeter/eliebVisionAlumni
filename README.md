# Elieb Vision Alumni
## Tasks
### Symfony Application Setup
- Set up a Symfony project.
- Configure the database connection.
- Install necessary Symfony bundles.

### Entity Creation
- Create entities for users, roles, permissions, events, subscriptions, messages, and images.
- Define relationships between entities.

### User Roles and Permissions
- Implement role-based access control (RBAC) using Symfony's security component.
- Define access control rules based on user roles.

### Chat Functionality
- Implement chat functionality for group and private messaging.
- Integrate real-time messaging using WebSockets for instant messaging.

### Subscription Management
- Create functionality for managing user subscriptions.
- Implement monthly subscription tracking.

### Image Upload for User Profiles and Messages
- Add an Image entity.
- Create forms and controllers to handle image uploads for user profiles and messages.
- Configure the file storage and upload directory.

## Entity List

- **User**
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
  - profileImage (Many-to-One relationship with Image entity)

- **Role**
  - id
  - name

- **Event**
  - id
  - title
  - description
  - date

- **Message**
  - id
  - sender (Many-to-One relationship with User entity)
  - receiver (Many-to-One relationship with User entity)
  - content
  - timestamp
  - images (One-to-Many relationship with Image entity)

- **Payout**
  - id
  - user (Many-to-One relationship with User entity)
  - payout_date
  - amount

- **NextOfKins**
  - id
  - firstNames
  - surname
  - email
  - phone_number

- **Image**
  - id
  - filename
  - path

## Cahier de Charge

### General Description
The application aims to facilitate communication and management within a group. It allows users to register, maintain their profiles, subscribe to monthly services, participate in events, and communicate via group and private messaging.

### Functionalities
1. **User Management**
   - Registration with basic information (firstNames, surname, email, phone_number, unique_id).
   - Authentication and password management.
   - Role-based access control (Admin, Treasurer, Vice President, Secretary, Senior Member, Regular User).
2. **Subscription Management**
   - Monthly subscription tracking for members.
   - Ability for Treasurer to update subscription status.
3. **Event Management**
   - Creation, viewing, and participation in events.
4. **Messaging**
   - Group messaging for all members.
   - Private messaging between individual members.
   - Support for attaching images to messages.
5. **Real-time Communication**
   - Integration of WebSockets for real-time messaging.
6. **Monthly Payout Tracking for Members**
   - Implement functionality to track monthly payouts for each member.
   - Ability for Treasurer to update payout details.
   - Viewing payout details: Regular users can view their own payout details.
7. **Profile Image Upload**
   - Allow users to upload and update profile images.
   - Display user profile images in relevant views.

### Security
1. **Authentication**
   - Secure user authentication and session management.
   - Password hashing and salting.
2. **Authorization**
   - Role-based access control for different functionalities.
   - Granular permissions for each role.
3. **Data Protection**
   - Encryption of sensitive data.
   - Protection against SQL injection and other security vulnerabilities.
4. **Compliance**
   - GDPR compliance for handling user data.
   - Secure transmission of data over HTTPS.

### Technical Requirements
1. **Symfony Framework**
   - Latest stable version.
   - Utilize Symfony Flex for project management.
2. **React.js with Vite**
   - Frontend developed using React.js with Vite for fast development.
   - Responsive design for optimal user experience across devices.
3. **Database**
   - MySQL or PostgreSQL for data storage.
4. **Real-time Messaging**
   - Integration of WebSockets using Symfony's Mercure component or alternative solutions like Pusher or Socket.io.
5. **File Uploads**
   - Configure Symfony to handle file uploads.
   - Store images in a designated directory.
6. **Deployment**
   - Deployment on a secure server with HTTPS support.
   - Utilize Docker for containerization if needed.
