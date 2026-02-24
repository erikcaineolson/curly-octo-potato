import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useCalculations } from '../useCalculations.js';

// Mock the api module
vi.mock('../../services/api.js', () => ({
    fetchCalculations: vi.fn(),
    createCalculation: vi.fn(),
    deleteCalculation: vi.fn(),
    clearAllCalculations: vi.fn(),
}));

import {
    fetchCalculations,
    createCalculation,
    deleteCalculation,
    clearAllCalculations,
} from '../../services/api.js';

describe('useCalculations', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('initializes with provided data', () => {
        const initial = [{ id: 1, expression: '1+1', result: 2 }];
        const { calculations } = useCalculations(initial);
        expect(calculations.value).toEqual(initial);
    });

    it('initializes with empty array by default', () => {
        const { calculations } = useCalculations();
        expect(calculations.value).toEqual([]);
    });

    it('refresh() fetches and updates calculations', async () => {
        const mockData = { data: [{ id: 1 }, { id: 2 }] };
        fetchCalculations.mockResolvedValue(mockData);

        const { calculations, refresh } = useCalculations();
        await refresh();

        expect(fetchCalculations).toHaveBeenCalledOnce();
        expect(calculations.value).toEqual(mockData.data);
    });

    it('refresh() sets error on failure', async () => {
        fetchCalculations.mockRejectedValue(new Error('Network error'));

        const { error, refresh } = useCalculations();
        await refresh();

        expect(error.value).toBe('Network error');
    });

    it('calculate() sends operands and prepends result', async () => {
        const mockResponse = { data: { id: 5, expression: '2 + 3', result: 5 } };
        createCalculation.mockResolvedValue(mockResponse);

        const { calculations, calculate } = useCalculations();
        const result = await calculate(2, 3, 'add');

        expect(createCalculation).toHaveBeenCalledWith({
            operand_a: 2,
            operand_b: 3,
            operator: 'add',
        });
        expect(result).toEqual(mockResponse.data);
        expect(calculations.value[0]).toEqual(mockResponse.data);
    });

    it('calculate() sets error on failure', async () => {
        const err = new Error('Validation failed');
        err.data = { message: 'Division by zero.' };
        createCalculation.mockRejectedValue(err);

        const { error, calculate } = useCalculations();
        await expect(calculate(1, 0, 'divide')).rejects.toThrow();
        expect(error.value).toBe('Division by zero.');
    });

    it('evaluateExpression() sends expression and prepends result', async () => {
        const mockResponse = { data: { id: 6, expression: '2^3', result: 8 } };
        createCalculation.mockResolvedValue(mockResponse);

        const { calculations, evaluateExpression } = useCalculations();
        const result = await evaluateExpression('2^3');

        expect(createCalculation).toHaveBeenCalledWith({ expression: '2^3' });
        expect(result).toEqual(mockResponse.data);
        expect(calculations.value[0]).toEqual(mockResponse.data);
    });

    it('removeOne() deletes and filters from list', async () => {
        deleteCalculation.mockResolvedValue(null);

        const initial = [
            { id: 1, expression: '1+1', result: 2 },
            { id: 2, expression: '2+2', result: 4 },
        ];
        const { calculations, removeOne } = useCalculations(initial);
        await removeOne(1);

        expect(deleteCalculation).toHaveBeenCalledWith(1);
        expect(calculations.value).toEqual([{ id: 2, expression: '2+2', result: 4 }]);
    });

    it('clearAll() empties the list', async () => {
        clearAllCalculations.mockResolvedValue(null);

        const { calculations, clearAll } = useCalculations([{ id: 1 }]);
        await clearAll();

        expect(clearAllCalculations).toHaveBeenCalledOnce();
        expect(calculations.value).toEqual([]);
    });

    it('clearError() resets the error', () => {
        const { error, clearError } = useCalculations();
        error.value = 'something broke';
        clearError();
        expect(error.value).toBeNull();
    });

    it('sets loading during async operations', async () => {
        let resolvePromise;
        createCalculation.mockReturnValue(
            new Promise((resolve) => {
                resolvePromise = resolve;
            })
        );

        const { loading, calculate } = useCalculations();
        expect(loading.value).toBe(false);

        const promise = calculate(1, 2, 'add');
        expect(loading.value).toBe(true);

        resolvePromise({ data: { id: 1, result: 3 } });
        await promise;
        expect(loading.value).toBe(false);
    });
});
