<!DOCTYPE html>
<html>
<head>
    <title>Upload Video</title>
</head>
<body>
    <h1>Upload New Video</h1>
    <form action="index.php?action=upload" method="POST" enctype="multipart/form-data">
        <input type="file" name="video" accept="video/*" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>