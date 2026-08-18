export function toFormBody(data: object): string {
  const params = new URLSearchParams();

  Object.entries(data as Record<string, string | number | undefined | null>).forEach(([key, value]) => {
    if (value !== undefined && value !== null) {
      params.set(key, String(value));
    }
  });

  return params.toString();
}

export function formUrlEncodedHeaders() {
  return { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } };
}
