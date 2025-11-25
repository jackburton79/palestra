export function showLoading(isLoading) {
  const loader = document.getElementById("loading");
  loader.classList.toggle("hidden", !isLoading);
}

export function showError(message) {
  const err = document.getElementById("error");
  err.textContent = message;
  err.classList.remove("hidden");
}

export function clearError() {
  const err = document.getElementById("error");
  err.textContent = "";
  err.classList.add("hidden");
}

export function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString() + " " + d.toLocaleTimeString();
}
