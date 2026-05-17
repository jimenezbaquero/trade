export function getColumnStyle(col) {

  if (col.auto) {
    return {}
  }

  return {
    width: col.width,
    minWidth: col.width,
    maxWidth: col.width,
  }
}