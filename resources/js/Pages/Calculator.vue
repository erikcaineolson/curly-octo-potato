<template>
    <main class="min-h-screen bg-gray-950 text-white flex items-center justify-center p-4" @keydown="handleKeyDown" ref="calculatorRef">
        <div class="w-full max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold tracking-tight">
                    <span class="text-blue-400">Calc</span><span class="text-amber-400">Tek</span>
                </h1>
                <p class="text-gray-400 text-xs mt-1">API-Driven Calculator</p>
            </div>

            <!-- Error Toast -->
            <Transition name="fade">
                <div
                    v-if="error"
                    id="expression-error"
                    role="alert"
                    aria-live="assertive"
                    class="mb-4 bg-red-900/50 border border-red-700 text-red-200 px-4 py-2 rounded-lg text-sm flex items-center justify-between"
                >
                    <span>{{ error }}</span>
                    <button @click="clearError" aria-label="Dismiss error" class="text-red-400 hover:text-red-300 ml-2">&times;</button>
                </div>
            </Transition>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Calculator -->
                <div class="md:col-span-3 bg-gray-900 rounded-2xl p-4 shadow-2xl shadow-black/50">
                    <!-- Mode Toggle -->
                    <div class="flex rounded-lg bg-gray-800 p-1 mb-4" role="tablist" aria-label="Calculator mode">
                        <button
                            @click="mode = 'simple'"
                            role="tab"
                            :aria-selected="mode === 'simple'"
                            :class="[
                                'flex-1 py-1.5 text-xs font-medium rounded-md transition-all',
                                mode === 'simple'
                                    ? 'bg-gray-700 text-white shadow-sm'
                                    : 'text-gray-300 hover:text-gray-200',
                            ]"
                        >
                            Standard
                        </button>
                        <button
                            @click="mode = 'expression'"
                            role="tab"
                            :aria-selected="mode === 'expression'"
                            :class="[
                                'flex-1 py-1.5 text-xs font-medium rounded-md transition-all',
                                mode === 'expression'
                                    ? 'bg-gray-700 text-white shadow-sm'
                                    : 'text-gray-300 hover:text-gray-200',
                            ]"
                        >
                            Expression
                        </button>
                    </div>

                    <!-- Simple Mode -->
                    <template v-if="mode === 'simple'">
                        <CalculatorDisplay
                            :expression="displayExpression"
                            :currentValue="currentValue"
                            :result="displayResult"
                        />
                        <div class="mt-4">
                            <CalculatorKeypad
                                @digit="handleDigit"
                                @decimal="handleDecimal"
                                @operator="handleOperator"
                                @equals="handleEquals"
                                @clear="handleClear"
                                @clearEntry="handleClearEntry"
                            />
                        </div>
                    </template>

                    <!-- Expression Mode -->
                    <template v-else>
                        <div class="bg-gray-900 rounded-xl p-4 font-mono mb-4">
                            <label for="expression-input" class="text-gray-400 text-xs block mb-2">Enter expression</label>
                            <input
                                id="expression-input"
                                v-model="expressionInput"
                                @keydown.enter="handleExpressionSubmit"
                                type="text"
                                placeholder="sqrt(((9*9)/12+(13-4))*2)^2"
                                :aria-describedby="error ? 'expression-error' : undefined"
                                class="w-full bg-gray-800 text-white text-lg font-mono rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-500"
                            />
                            <!-- Colorized paren preview -->
                            <ExpressionPreview :expression="expressionInput" />
                            <div v-if="expressionResult !== null" aria-live="polite" class="text-right text-2xl font-bold text-white mt-2">
                                = {{ formatNumber(expressionResult) }}
                            </div>
                        </div>
                        <button
                            @click="handleExpressionSubmit"
                            :disabled="!expressionInput.trim() || loading"
                            class="w-full bg-blue-600 hover:bg-blue-500 disabled:bg-gray-700 disabled:text-gray-500 text-white font-semibold py-3 rounded-lg transition-colors mb-4"
                        >
                            {{ loading ? 'Calculating...' : 'Calculate' }}
                        </button>

                        <!-- Supported operations reference -->
                        <div class="bg-gray-800/50 rounded-xl p-3 text-xs text-gray-400 space-y-2">
                            <div class="text-gray-300 font-semibold text-xs uppercase tracking-wider mb-1">Supported Operations</div>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                <div><span class="text-amber-400 font-mono">+</span> Addition</div>
                                <div><span class="text-amber-400 font-mono">-</span> Subtraction</div>
                                <div><span class="text-amber-400 font-mono">*</span> Multiplication</div>
                                <div><span class="text-amber-400 font-mono">/</span> Division</div>
                                <div><span class="text-amber-400 font-mono">^</span> Exponent <span class="text-gray-400">(2^3 = 8)</span></div>
                                <div><span class="text-amber-400 font-mono">sqrt()</span> Square root</div>
                                <div><span class="text-amber-400 font-mono">( )</span> Parentheses</div>
                                <div><span class="text-amber-400 font-mono">-x</span> Unary minus</div>
                            </div>
                            <div class="border-t border-gray-700 pt-2 mt-2 text-gray-400 leading-relaxed">
                                Decimals supported. Operator precedence is respected.
                                <br>
                                Not supported: variables, matrices, trigonometry, logarithms, derivatives, integrals, or any symbolic algebra.
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Ticker Tape -->
                <div class="md:col-span-2 bg-gray-900 rounded-2xl p-4 shadow-2xl shadow-black/50 h-[28rem] flex flex-col">
                    <TickerTape
                        :calculations="calculations"
                        @deleteOne="removeOne"
                        @clearAll="clearAll"
                    />
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { ref, computed } from 'vue';
import CalculatorDisplay from '../Components/CalculatorDisplay.vue';
import CalculatorKeypad from '../Components/CalculatorKeypad.vue';
import ExpressionPreview from '../Components/ExpressionPreview.vue';
import TickerTape from '../Components/TickerTape.vue';
import { useCalculations } from '../composables/useCalculations.js';
import { formatNumber } from '../utils/formatNumber.js';

const props = defineProps({
    calculations: { type: Object, default: () => ({ data: [] }) },
});

const {
    calculations,
    loading,
    error,
    clearError,
    calculate,
    evaluateExpression,
    removeOne,
    clearAll,
} = useCalculations(props.calculations?.data || []);

const calculatorRef = ref(null);

// Mode
const mode = ref('simple');

// Simple mode state
const currentValue = ref('0');
const pendingOperator = ref(null);
const pendingOperand = ref(null);
const displayResult = ref(null);
const waitingForOperand = ref(false);

// Expression mode state
const expressionInput = ref('');
const expressionResult = ref(null);

const displayExpression = computed(() => {
    if (pendingOperand.value !== null && pendingOperator.value) {
        const symbol = { add: '+', subtract: '\u2212', multiply: '\u00d7', divide: '/' }[pendingOperator.value];
        return `${pendingOperand.value} ${symbol}`;
    }
    return '';
});

// Keyboard support
function handleKeyDown(e) {
    if (mode.value !== 'simple') return;
    if (e.target.tagName === 'INPUT') return;

    const key = e.key;

    if (/^[0-9]$/.test(key)) {
        handleDigit(key);
    } else if (key === '.') {
        handleDecimal();
    } else if (key === '+') {
        handleOperator('add');
    } else if (key === '-') {
        handleOperator('subtract');
    } else if (key === '*') {
        handleOperator('multiply');
    } else if (key === '/') {
        e.preventDefault();
        handleOperator('divide');
    } else if (key === 'Enter' || key === '=') {
        handleEquals();
    } else if (key === 'Escape') {
        handleClear();
    } else if (key === 'Backspace') {
        handleClearEntry();
    }
}

function handleDigit(digit) {
    if (displayResult.value !== null) {
        currentValue.value = digit;
        displayResult.value = null;
        pendingOperand.value = null;
        pendingOperator.value = null;
        waitingForOperand.value = false;
        return;
    }

    if (waitingForOperand.value) {
        currentValue.value = digit;
        waitingForOperand.value = false;
    } else {
        currentValue.value = currentValue.value === '0' ? digit : currentValue.value + digit;
    }
}

function handleDecimal() {
    if (displayResult.value !== null) {
        currentValue.value = '0.';
        displayResult.value = null;
        pendingOperand.value = null;
        pendingOperator.value = null;
        waitingForOperand.value = false;
        return;
    }

    if (waitingForOperand.value) {
        currentValue.value = '0.';
        waitingForOperand.value = false;
        return;
    }

    if (!currentValue.value.includes('.')) {
        currentValue.value += '.';
    }
}

function handleOperator(operator) {
    const value = parseFloat(currentValue.value);

    if (pendingOperand.value !== null && pendingOperator.value && !waitingForOperand.value) {
        performCalculation(pendingOperand.value, value, pendingOperator.value);
    }

    pendingOperand.value = displayResult.value !== null ? parseFloat(displayResult.value) : value;
    pendingOperator.value = operator;
    waitingForOperand.value = true;
    displayResult.value = null;
}

async function handleEquals() {
    if (pendingOperand.value === null || !pendingOperator.value) return;

    const operandB = parseFloat(currentValue.value);
    await performCalculation(pendingOperand.value, operandB, pendingOperator.value);

    pendingOperand.value = null;
    pendingOperator.value = null;
    waitingForOperand.value = false;
}

async function performCalculation(a, b, operator) {
    try {
        const result = await calculate(a, b, operator);
        displayResult.value = String(result.result);
        currentValue.value = String(result.result);
    } catch {
        // error is handled by composable
    }
}

function handleClear() {
    currentValue.value = '0';
    pendingOperator.value = null;
    pendingOperand.value = null;
    displayResult.value = null;
    waitingForOperand.value = false;
}

function handleClearEntry() {
    currentValue.value = '0';
    displayResult.value = null;
}

async function handleExpressionSubmit() {
    if (!expressionInput.value.trim()) return;

    try {
        const result = await evaluateExpression(expressionInput.value.trim());
        expressionResult.value = result.result;
    } catch {
        expressionResult.value = null;
    }
}

</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
