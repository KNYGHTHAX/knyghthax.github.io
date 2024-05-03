const longUrlInput = document.getElementById('longUrl');
const shortenBtn = document.getElementById('shortenBtn');
const shortenedUrlContainer = document.getElementById('shortenedUrlContainer');
const shortenedUrlLink = document.getElementById('shortenedUrl');

const baseUrl = 'localhost/'; // Replace with your domain

shortenBtn.addEventListener('click', () => {
  const longUrl = longUrlInput.value;
  if (longUrl) {
    const shortUrl = generateShortUrl();
    shortenedUrlLink.href = baseUrl + shortUrl;
    shortenedUrlLink.textContent = baseUrl + shortUrl;
    shortenedUrlContainer.style.display = 'block';
  }
});

function generateShortUrl() {
  const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  let shortUrl = '';
  for (let i = 0; i < 6; i++) {
    shortUrl += chars[Math.floor(Math.random() * chars.length)];
  }
  return shortUrl;
}