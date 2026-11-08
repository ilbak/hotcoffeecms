<img src="https://a.fsdn.com/allura/p/hotcoffeecms/icon?1509012589">

<b>Hot Coffee</b> is a simple CMS written in PHP that does not require any additional element occupies only 4 kb.

Engaging directly with the models in HTML, allows you to quickly create a website compatible with all standard HTML and CSS.

---
# Getting started...

- Upload the hotcoffeecms.php and index.php file to your web space
- Open index.php from the browser and your site will be active.

The first page that will be created automatically will be called "Home" and will be contained in the "Home.php" file.
Change your home file to your liking and create a php file for each page that will make up your new site.

---
# Site structure

The structure of the site is designed in a minimalist way:
The file hotcoffeecms.php, index.php and Home.php will be in the main directory and every new PHP file in the folder will be counted by the CMS as a new page and automatically listed in the navigation bar.

If you want to create secondary, unlisted pages, add the "-none.php" suffix to the page file name.
<i>For example: "Contact.php" will be listed in the navigation bar, renaming it to "Contact-none.php" will not be listed.</i>

---
# Widget
The php files with the suffix "-sx.php" (left) and "-dx.php" (right) are loaded as a widget on the sides of the page.

---
# Templates
Hot Coffee CMS can be integrated into any theme, including the "hotcoffeecms.php" file in the "index.php" file that sets the theme.
You can use the "index.php" file on the Hot Coffee CMS website and modify it for your needs or learn how to include the CMS in other themes.
The "index.php" that you find already contains everything you need to set up your site, change the title, the colors and you're done!

---
# Hot Coffee Page
Download "hotcoffeepage.php" and upload it to your Hot Coffee website to have a page that can be edited using a convenient web interface.

---
# About
Hot Coffee comes from my desire for a fast, minimalist and highly adaptable system for the creation and management of websites.
A site created with Hot Coffe can be hosted in any hosting service or private server and can be coined and exported in seconds.
For information and help you can contact me via the web: <a href="https://www.ilbak.it/">www.ilbak.it</a>.

---

## Core Features and Functions with Usage Examples

### 1. Temporary Error Management (`cmserror`)
Stores a timestamp in the session (`$_SESSION['cmserror']`) to rate-limit requests when a critical error occurs, blocking repeated hits for 2 minutes before halting execution.

**Usage Example:**
```php
if (!is_database_connected()) {
    cmserror("<h1>Database Connection Failed</h1><p>Please try again in 2 minutes.</p>");
}
```

---

### 2. System Logging (`addlog`)
Appends log messages to a designated system file (limiting history to a maximum of 100 lines) and simultaneously outputs the log to the browser's JavaScript console.

**Usage Example:**
```php
addlog("User #42 successfully updated their profile settings.");
```

---

### 3. Client IP Address Detection (`getRealIp`)
Inspects various server headers (`$_SERVER`) to accurately identify the real IP address of the client, accommodating proxies and load balancers.

**Usage Example:**
```php
$userIp = getRealIp();
echo "Your IP address is: " . htmlspecialchars($userIp);
```

---

### 4. Entry Point Enforcement
Ensures that the execution flow passes through `index.php`. If a user attempts to access scripts directly, it automatically redirects them back to the main index.

**Usage Example:**
```php
// If accessed directly via 'https://example.com/about.php', 
// hotcoffeecms.php forces a 302 redirect to:
// Location: index.php
```

---

### 5. Multilingual Session Management
Sets the current session language based on the incoming `lang` URL parameter. It validates against an allowed whitelist (`it`, `en`, `fr`, `de`, `es`), falling back to `it` (Italian) by default.

**Usage Example:**
```html
<!-- Navigating to a localized view -->
<a href="index.php?pag=about&lang=en">English</a>
<a href="index.php?pag=about&lang=es">Español</a>
```
```php
// In your custom template scripts:
if ($_SESSION['lang'] === 'en') {
    echo "Welcome!";
} else {
    echo "Benvenuto!";
}
```

---

### 6. Protocol Detection & Global Base URL Calculation
Detects whether the connection is served over HTTP or HTTPS (including reverse proxies) and defines `$GLOBALS['cmsurl']` for global absolute URL referencing.

**Usage Example:**
```php
// Automatically outputs the full URL regardless of domain or protocol
echo '<img src="' . $GLOBALS['cmsurl'] . 'assets/logo.png" alt="Logo">';
// Output: https://example.com/assets/logo.png
```

---

### 7. Dynamic Routing and Page Sanitization
Handles navigation using the `pag` URL parameter. It applies strict sanitization (allowing only alphanumeric characters and hyphens) and defaults to `home`. If a page is not found, it attempts to load `404.php` or creates a default `home.php`.

**Usage Example:**
```html
<!-- URL Request: index.php?pag=about-us -->
```
```php
// Hot Coffee CMS sanitizes $_REQUEST['pag'] to "about-us" 
// and includes: /path/to/cms/about-us.php
```

---

### 8. Tag Parameter Parsing
Parses dash-separated tags from the `tag` URL parameter into a globally accessible array `$GLOBALS['tag']`.

**Usage Example:**
```html
<!-- URL Request: index.php?pag=blog&tag=php-cms-tutorial -->
```
```php
// Accessing tags inside your page script:
print_r($GLOBALS['tag']);
// Output: Array ( [0] => php [1] => cms [2] => tutorial )
```

---

### 9. Automatic Page Asset Inclusion (CSS / JS)
Checks for page-specific assets sharing the same filename as the active page (e.g., `about.css` or `about.js`) and automatically injects them into the document output.

**File Structure Example:**
```text
htdocs/
├── index.php
├── hotcoffeecms.php
├── Home.php
├── Home.css  <-- Automatically included on 'contact' page
└── Home.js   <-- Automatically included on 'contact' page
```

---

### 10. Modular Block and Side-View Rendering
Renders menu links or dynamically includes module files when `$GLOBALS['cms']` is defined prior to script execution.

**Usage Example (Menu Generation):**
```php
// Define custom menu items before hotcoffeecms.php completes execution
$GLOBALS['cms'] = ['home', 'about', 'services', 'contact'];

// hotcoffeecms.php outputs:
// <ul>
//   <li><a href='/'>home</a></li>
//   <li><a href='/about'>about</a></li>
//   ...
// </ul>
```
