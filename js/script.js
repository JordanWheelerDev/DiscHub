// Get the current path without double slashes
var currentPath = window.location.pathname.replace(/\/{2,}/g, "/");

// Redirect to the cleaned path if it has changed
if (currentPath !== window.location.pathname) {
  window.location.replace(window.location.origin + currentPath);
}
