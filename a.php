<!DOCTYPE html>
<html>
  <head>
    <title>URL Shortener</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <h1>URL Shortener</h1>
    <div id="shortenerContainer">
      <input type="text" id="longUrl" placeholder="Enter a long URL" />
      <button id="shortenBtn">Shorten URL</button>
    </div>
    <div id="shortenedUrlContainer" style="display: none;">
      <p>Your shortened URL is:</p>
      <a id="shortenedUrl" target="_blank"></a>
    </div>
    <script src="script.js"></script>
  </body>
</html>