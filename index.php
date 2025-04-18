<?php
// Connect to MySQL using MAMP credentials
$conn = new mysqli("localhost", "root", "root", "DATABASEpage_creator");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Message variable
$message = "";

// Handle create form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create'])) {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $slug = $conn->real_escape_string(trim($_POST['slug']));
    $desc = $conn->real_escape_string(trim($_POST['description']));
    $domain = $conn->real_escape_string(trim($_POST['custom_domain']));

    if (!$title || !$slug || !$desc) {
        $message = "<p class='error'>❌ Please fill in all required fields.</p>";
    } else {
        $sql = "INSERT INTO pages (title, slug, description, custom_domain)
                VALUES ('$title', '$slug', '$desc', '$domain')";

        $message = $conn->query($sql)
            ? "<p class='success'>✅ Page saved successfully!</p>"
            : "<p class='error'>❌ Error: " . $conn->error . "</p>";
    }
}

// Handle search query
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if ($search !== '') {
    $safeSearch = $conn->real_escape_string($search);
    $sql = "SELECT * FROM pages 
            WHERE title LIKE '%$safeSearch%' 
               OR slug LIKE '%$safeSearch%' 
               OR description LIKE '%$safeSearch%' 
               OR custom_domain LIKE '%$safeSearch%'";
    $query = $conn->query($sql);
    if ($query) {
        $results = $query->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Page Creator & Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f8f9fa;
        }
        h2, h3 { color: #333; }
        form {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            max-width: 600px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        input[type="submit"], button {
            background: #007bff;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .seo-box {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 15px;
            margin-top: 10px;
            max-width: 600px;
        }
        .seo-title { color: #1a0dab; font-size: 18px; font-weight: bold; }
        .seo-url { color: #006621; }
        .result-item {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .success { color: green; }
        .error { color: red; }
        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 30px 0;
        }
    </style>
</head>
<body>

<h2>📝 Create New Page</h2>
<?php echo $message; ?>

<form method="POST">
    <input type="hidden" name="create" value="1">

    <label>Title</label>
    <input type="text" name="title" id="title" oninput="updatePreview()" required>

    <label>Slug</label>
    <input type="text" name="slug" id="slug" oninput="updatePreview()" required>

    <label>Description</label>
    <textarea name="description" id="description" oninput="updatePreview()" required></textarea>

    <label>Custom Domain</label>
    <input type="text" name="custom_domain" id="custom_domain" oninput="updatePreview()">

    <input type="submit" value="Save Page">
</form>

<h3>🔍 SEO Preview</h3>
<div class="seo-box">
    <p class="seo-title" id="previewTitle">Page Title</p>
    <p class="seo-url" id="previewURL">www.example.com/your-slug</p>
    <p id="previewDesc">Meta description will appear here...</p>
</div>

<script>
function updatePreview() {
    const title = document.getElementById('title').value || 'Page Title';
    const slug = document.getElementById('slug').value || 'your-slug';
    const desc = document.getElementById('description').value || 'Meta description will appear here...';
    const domain = document.getElementById('custom_domain').value || 'www.example.com';

    document.getElementById('previewTitle').textContent = title;
    document.getElementById('previewURL').textContent = `${domain}/${slug}`;
    document.getElementById('previewDesc').textContent = desc;
}
</script>

<hr>

<h2>🔎 Search Pages</h2>
<form method="GET">
    <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search..." required>
    <button type="submit">Search</button>
</form>

<?php if ($search): ?>
    <h3>Search Results for "<em><?php echo htmlspecialchars($search); ?></em>":</h3>
    <?php if (count($results)): ?>
        <?php foreach ($results as $page): ?>
            <div class="result-item">
                <strong><?php echo htmlspecialchars($page['title']); ?></strong><br>
                <small><?php echo htmlspecialchars($page['custom_domain'] ?: 'www.example.com') . '/' . htmlspecialchars($page['slug']); ?></small>
                <p><?php echo htmlspecialchars($page['description']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No results found.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
<!-- hello -->
