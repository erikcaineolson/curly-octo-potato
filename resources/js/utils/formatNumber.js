export function formatNumber(value) {
    const num = parseFloat(value);
    if (isNaN(num)) return String(value);
    if (Number.isInteger(num) && Math.abs(num) < 1e15) {
        return num.toLocaleString('en-US');
    }
    return num.toLocaleString('en-US', { maximumFractionDigits: 10 });
}
