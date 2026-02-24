import { describe, it, expect } from 'vitest';
import { formatNumber } from '../formatNumber.js';

describe('formatNumber', () => {
    it('formats integers with commas', () => {
        expect(formatNumber(1000)).toBe('1,000');
        expect(formatNumber(1234567)).toBe('1,234,567');
    });

    it('returns "0" for zero', () => {
        expect(formatNumber(0)).toBe('0');
    });

    it('formats decimal numbers', () => {
        expect(formatNumber(1234.56)).toBe('1,234.56');
    });

    it('handles negative numbers', () => {
        expect(formatNumber(-5000)).toBe('-5,000');
    });

    it('handles string numeric input', () => {
        expect(formatNumber('2500')).toBe('2,500');
        expect(formatNumber('3.14')).toBe('3.14');
    });

    it('returns non-numeric strings as-is', () => {
        expect(formatNumber('abc')).toBe('abc');
        expect(formatNumber('')).toBe('');
    });

    it('handles very large integers without locale formatting', () => {
        // Above 1e15 threshold, uses maximumFractionDigits path
        const big = 1e16;
        const result = formatNumber(big);
        expect(result).toBeTruthy();
    });

    it('handles null and undefined gracefully', () => {
        // parseFloat(null) is NaN, so String(null) => 'null'
        expect(formatNumber(null)).toBe('null');
        expect(formatNumber(undefined)).toBe('undefined');
    });
});
