import { ref } from 'vue';
import {
    fetchCalculations,
    createCalculation,
    deleteCalculation,
    clearAllCalculations,
} from '../services/api.js';

export function useCalculations(initial = []) {
    const calculations = ref(initial);
    const loading = ref(false);
    const error = ref(null);

    function clearError() {
        error.value = null;
    }

    async function refresh() {
        loading.value = true;
        error.value = null;
        try {
            const response = await fetchCalculations();
            calculations.value = response.data;
        } catch (e) {
            error.value = e.message;
        } finally {
            loading.value = false;
        }
    }

    async function calculate(operandA, operandB, operator) {
        loading.value = true;
        error.value = null;
        try {
            const response = await createCalculation({
                operand_a: operandA,
                operand_b: operandB,
                operator,
            });
            calculations.value.unshift(response.data);
            return response.data;
        } catch (e) {
            error.value = e.data?.message || e.message;
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function evaluateExpression(expression) {
        loading.value = true;
        error.value = null;
        try {
            const response = await createCalculation({ expression });
            calculations.value.unshift(response.data);
            return response.data;
        } catch (e) {
            error.value = e.data?.message || e.message;
            throw e;
        } finally {
            loading.value = false;
        }
    }

    async function removeOne(id) {
        error.value = null;
        try {
            await deleteCalculation(id);
            calculations.value = calculations.value.filter((c) => c.id !== id);
        } catch (e) {
            error.value = e.message;
        }
    }

    async function clearAll() {
        error.value = null;
        try {
            await clearAllCalculations();
            calculations.value = [];
        } catch (e) {
            error.value = e.message;
        }
    }

    return {
        calculations,
        loading,
        error,
        clearError,
        refresh,
        calculate,
        evaluateExpression,
        removeOne,
        clearAll,
    };
}
