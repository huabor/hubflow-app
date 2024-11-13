export const fc = (value: number, currency: string = 'EUR'): string =>
    new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: currency,
    }).format(value);
