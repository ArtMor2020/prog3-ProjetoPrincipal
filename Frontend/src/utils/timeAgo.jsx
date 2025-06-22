export default function timeAgo(timestamp) {
  let dateString = timestamp;

  if (typeof timestamp === "object" && timestamp !== null && timestamp.date) {
    dateString = timestamp.date;
  }

  if (!dateString || typeof dateString !== "string") {
    return "algum tempo";
  }

  const then = new Date(dateString.replace(" ", "T") + "Z").getTime();
  const now = Date.now();
  const diff = now - then;

  const seconds = Math.floor(diff / 1000);
  if (seconds < 60) return `${seconds} seg`;

  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes} min`;

  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours} h`;

  const days = Math.floor(hours / 24);
  if (days < 7) return `${days} d`;

  const weeks = Math.floor(days / 7);
  if (weeks < 5) return `${weeks} sem`;

  const months = Math.floor(days / 30);
  if (months < 12) return `${months} m`;

  const years = Math.floor(months / 12);
  return `${years} a`;
}
