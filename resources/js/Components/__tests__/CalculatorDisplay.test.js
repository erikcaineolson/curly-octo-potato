import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import CalculatorDisplay from '../CalculatorDisplay.vue';

describe('CalculatorDisplay', () => {
    it('shows "0" when no props are provided', () => {
        const wrapper = mount(CalculatorDisplay);
        expect(wrapper.text()).toContain('0');
    });

    it('shows the current value when no result is set', () => {
        const wrapper = mount(CalculatorDisplay, {
            props: { currentValue: '42', result: null },
        });
        expect(wrapper.text()).toContain('42');
    });

    it('shows the formatted result when result is provided', () => {
        const wrapper = mount(CalculatorDisplay, {
            props: { currentValue: '0', result: '1500' },
        });
        expect(wrapper.text()).toContain('1,500');
    });

    it('shows the expression line when provided', () => {
        const wrapper = mount(CalculatorDisplay, {
            props: { expression: '9 + 3', currentValue: '3' },
        });
        expect(wrapper.text()).toContain('9 + 3');
    });

    it('renders non-breaking space when expression is empty', () => {
        const wrapper = mount(CalculatorDisplay, {
            props: { expression: '', currentValue: '0' },
        });
        // jsdom serializes \u00A0 as &nbsp; in innerHTML
        const expressionDiv = wrapper.find('.text-gray-400');
        expect(expressionDiv.element.textContent).toBe('\u00A0');
    });
});
