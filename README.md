### Instagram Clone Project

### Task
Create a web application that mimics the core functionalities of Instagram using Laravel for the backend and Vite.js for the frontend. This project aims to replicate features such as user authentication, posting images, liking posts, following users, and commenting on posts.

### Description
This project is an Instagram clone built with Laravel and Vite.js, two powerful technologies for backend and frontend development respectively. It offers a platform where users can sign up, upload images, follow other users, like and comment on posts, and explore content similar to Instagram.

### Installation

1. **Clone the Repository:**
   ```
   git clone https://github.com/yourusername/instagram-clone.git
   ```

2. **Navigate to Project Directory:**
   ```
   cd instagram-clone
   ```

3. **Install Dependencies:**
   ```
   composer install
   npm install
   ```

4. **Set Up Environment Variables:**
   - Copy the `.env.example` file and rename it to `.env`.
   - Update the necessary environment variables such as database credentials.

5. **Generate Application Key:**
   ```
   php artisan key:generate
   ```

6. **Run Migrations:**
   ```
   php artisan migrate
   ```

7. **Compile Assets:**
   ```
   npm run dev
   ```

8. **Start the Server:**
   ```
   php artisan serve
   ```

9. **Access the Application:**
   Open your browser and visit `http://localhost:8000`.

### Usage

1. **Sign Up / Sign In:**
   - Navigate to the application URL.
   - If you're a new user, sign up with your details.
   - If you're a returning user, sign in with your credentials.

2. **Upload Post:**
   - Click on the 'Upload' button.
   - Select an image from your device.
   - Add a caption if desired.
   - Click on 'Post' to share it with your followers.

3. **Interact:**
   - Like posts by clicking the heart icon.
   - Comment on posts by typing in the comment box and pressing 'Enter'.

4. **Follow Users:**
   - Visit the profile of a user you want to follow.
   - Click on the 'Follow' button to subscribe to their updates.

5. **Edit Profile:**
   - Click on your profile icon.
   - Select 'Edit Profile'.
   - Update your profile picture, bio, or any other information.

6. **Logout:**
   - Click on your profile icon.
   - Select 'Logout' to end your session.