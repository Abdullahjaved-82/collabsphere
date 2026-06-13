# 👥 CollabSphere — Student Collaboration Workspace

CollabSphere is a premium, high-fidelity student collaboration and project management workspace built using **Laravel**, **Tailwind CSS**, and **Alpine.js**. The platform features dynamic real-time project metrics, SortableJS-based drag-and-drop Kanban task boards, AI-powered task breakdowns using the Groq API (Llama 3.1 70B), responsive message center boards, a dynamic global search overlay (`Ctrl+K`), custom toast notifications, and an automated multi-step user onboarding flow.

---

## 🚀 Core Functionalities

### 1. Unified Analytics Dashboard (`/dashboard`)
*   **Animated Stat Cards**: Modern top-row metric cards ("Total Tasks", "Completed", "In Progress", "Overdue") with vibrant backdrops and a smooth `1.2s` numeric count-up animation powered by `requestAnimationFrame`.
*   **IntersectionObserver Lazy-Loaded Charts**: 
    *   *Project Progress*: A horizontal bar chart showing completion % per project.
    *   *Task Velocity*: A minimalist line graph sparkline showing tasks completed over the last 14 days, complete with smooth cubic-bezier curves and soft background gradients.
    *   *Member Workload*: A doughnut chart illustrating current active task assignment distribution among teammates.
*   **Recent Activity Sidebar**: A live vertical audit log pulling recent task creations and drag-and-drop movements with relative time stamps (e.g. "3 minutes ago").
*   **Mock Auto-Seeder**: Instantly populates your dashboard with high-fidelity projects, tasks, and historical activity feeds on newly created teams.

### 2. Collapsible My Tasks Workspace (`/my-tasks`)
*   **Project Grouping Accordions**: Groups all your assigned tasks by project in clean collapsible cards with animated rotating chevron indicators.
*   **Live Filter Tabs**: Filter bar featuring **All**, **Todo**, **In Progress**, and **Overdue** tabs, with real-time numeric badges displaying active counts for each category.
*   **Inline AJAX Checkboxes**: Designed custom round circular checkboxes. Checking a box instantly strikes through the task title, updates the status badge, recalculates total stats, and updates the database via an AJAX PATCH request.
*   **Overdue Highlights**: Tasks past their target dates are flagged with soft red backgrounds (`rgba(239, 68, 68, 0.04)`) and bold red clock indicators.

### 3. SortableJS Kanban Board (`/projects/{project}/kanban`)
*   **Interactive Columns**: Drag-and-drop tasks between columns (*To Do*, *In Progress*, *Review*, *Done*) with smooth cubic-bezier transitions.
*   **Visual Highlights**: Highlights drop target columns dynamically during active card drags.
*   **Task Creator Modals**: Quick-action modal inside the Kanban board to instantly create, save, and inject new tasks into the column layout without full page refreshes.
*   **Background Polling**: Auto-polls every 10 seconds to keep client boards perfectly synchronized across teammate browsers.

### 4. Groq AI Task Assistant (`/projects/{project}/ai-assistant`)
*   **Intelligent Breakdown**: Leverages the **Groq API** (Llama 3.1 70B model) to analyze project descriptions and generate structured, context-rich task breakdowns.
*   **Smart Task Assignment**: The AI analyzes the `Specialty` / `Expertise` of all available team members and automatically assigns generated tasks to the most suitable partner. It gracefully leaves tasks unassigned if no one fits the required skill profile.
*   **Creative Inference**: If a project lacks a detailed description, the AI uses its own inference engine to deduce the necessary development lifecycle steps based on the project title.
*   **Staggered Card Entrance**: Renders AI recommendations in a beautiful staggered sequence with category badges.
*   **Accept / Reject Transitions**: Lets users adjust details (deadlines, priorities, team assignments) inline, accepting fly-out animations or rejecting items.

### 5. Multi-Step Onboarding Wizard & Strict Profiles (`/onboarding`)
*   **Route Gating Middleware**: Custom middleware blocks all application screens and redirects newly registered users to `/onboarding`.
*   **Strict Specialty Enforcement**: Forces all users (new and legacy) to configure a mandatory "Specialty / Expertise" field before accessing the dashboard, guaranteeing the AI always has assignment context.
*   **3-Step Setup Wizard**:
    *   *Step 1*: Configure specialty, write a biography, and upload a teammate portrait.
    *   *Step 2*: Create a brand-new team workspace or join an existing team via an invite code.
    *   *Step 3*: Launch your first project inside the selected team.
*   **Completion Checkmarks**: Sets `has_completed_onboarding = true` upon completion and redirects to the dashboard.

### 6. Command-K Global Search Modal
*   **Instant Keyboard Overlay**: Pressing `Ctrl + K` or `Cmd + K` on any screen opens a blur-backdrop centered search modal.
*   **Debounced AJAX Search**: live search queries the API using a `300ms` input debounce.
*   **Categorized Results**: Groups matches under **Projects**, **Tasks**, and **Team Members** with custom category icons and description subtitles.
*   **Full Keyboard Navigation**: Move selection up/down with arrow keys (`ArrowUp` / `ArrowDown`) and press `Enter` to navigate to the resource.

### 7. Message Center & Announcements (`/messages`)
*   **Direct Mail**: Send private, direct messages to teammate inbox boards.
*   **Pinned Team Announcements**: Dedicated announcement drafts accessible only to Team Leaders, with options to globally pin priority news to the header banner for all members.

---

## 🛠 Prerequisites & System Requirements

Before running the project, ensure your environment meets the following software requirements:

*   **PHP**: `^8.2` or higher
*   **Composer**: Dependency Manager for PHP
*   **Node.js & NPM**: `^18.x` or higher (for compiling frontend assets)
*   **SQLite**: Database engine (configured by default for lightweight local development)

---

## 📦 Setup & Installation Guide

Follow these sequential steps to configure, install, and initialize CollabSphere locally:

### Step 1: Clone the Repository & Install PHP Dependencies
Navigate to your projects directory and install Composer dependencies:
```bash
composer install
```

### Step 2: Install Node.js Dependencies & Compile Assets
Download NPM dependencies and compile frontend production-ready assets:
```bash
npm install
npm run build
```

### Step 3: Configure Environment Variables
Copy the template `.env` file to create your active configurations:
```bash
copy .env.example .env
```

### Step 4: Generate Application Key
Generate a secure application key:
```bash
php artisan key:generate
```

### Step 5: Configure SQLite Database File
Create the lightweight SQLite database file referenced in the default `.env` configuration:
```bash
# Windows PowerShell command to create database file
New-Item -Path "database" -Name "database.sqlite" -ItemType "file" -Force
```

### Step 6: Execute Migrations
Run database migrations to generate all standard tables (Users, Teams, Projects, Tasks, Notifications, Messages, Activities):
```bash
php artisan migrate
```

---

## 🚦 Commands to Run the Project

To start your fully functional CollabSphere application, open two separate terminal sessions and execute the following commands:

### Terminal 1: Start Laravel Development Server
Start the core PHP backend web server:
```bash
php artisan serve
```
*The application will now be running locally at **`http://127.0.0.1:8000`**.*

### Terminal 2: Run Vite Dev Asset Server
Start the active asset hot-reloading dev server:
```bash
npm run dev
```

---

## 🧪 Verification & Testing
To execute the automated unit and feature test suites (Authentication, Registrations, Profile Updates, Password Resets) to ensure your workspace setup is 100% correct, run:
```bash
php artisan test
```
