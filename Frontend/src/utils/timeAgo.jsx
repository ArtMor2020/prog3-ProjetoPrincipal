export default function timeAgo(timestamp) {
  // transforma "YYYY-MM-DD HH:mm:ss" em "YYYY-MM-DDTHH:mm:ssZ"
  const then = new Date(timestamp.replace(' ', 'T') + 'Z').getTime();
  const now = Date.now();
  const diff = now - then;    // sempre positivo se o timestamp for passado

  const seconds = Math.floor(diff / 1000);
  if (seconds < 60) return `${seconds} sec`;

  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes} min`;

  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''}`;

  const days = Math.floor(hours / 24);
  if (days < 7) return `${days} day${days > 1 ? 's' : ''}`;

  const weeks = Math.floor(days / 7);
  if (weeks < 5) return `${weeks} week${weeks > 1 ? 's' : ''}`;

  const months = Math.floor(days / 30);
  if (months < 12) return `${months} month${months > 1 ? 's' : ''}`;

  const years = Math.floor(months / 12);
  return `${years} year${years > 1 ? 's' : ''}`;
}