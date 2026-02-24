import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import CalculatorKeypad from '../CalculatorKeypad.vue';

describe('CalculatorKeypad', () => {
    it('renders all keys', () => {
        const wrapper = mount(CalculatorKeypad);
        const buttons = wrapper.findAll('button');
        // 16 digits/ops + 2 action rows = 18 buttons
        expect(buttons.length).toBe(18);
    });

    it('emits "digit" when a number button is clicked', async () => {
        const wrapper = mount(CalculatorKeypad);
        const button7 = wrapper.findAll('button').find((b) => b.text() === '7');
        await button7.trigger('click');
        expect(wrapper.emitted('digit')).toBeTruthy();
        expect(wrapper.emitted('digit')[0]).toEqual(['7']);
    });

    it('emits "operator" with value when operator button is clicked', async () => {
        const wrapper = mount(CalculatorKeypad);
        const plusButton = wrapper.findAll('button').find((b) => b.text() === '+');
        await plusButton.trigger('click');
        expect(wrapper.emitted('operator')).toBeTruthy();
        expect(wrapper.emitted('operator')[0]).toEqual(['add']);
    });

    it('emits "equals" when = button is clicked', async () => {
        const wrapper = mount(CalculatorKeypad);
        const eqButton = wrapper.findAll('button').find((b) => b.text() === '=');
        await eqButton.trigger('click');
        expect(wrapper.emitted('equals')).toBeTruthy();
    });

    it('emits "decimal" when . button is clicked', async () => {
        const wrapper = mount(CalculatorKeypad);
        const dotButton = wrapper.findAll('button').find((b) => b.text() === '.');
        await dotButton.trigger('click');
        expect(wrapper.emitted('decimal')).toBeTruthy();
    });

    it('emits "clear" when C button is clicked', async () => {
        const wrapper = mount(CalculatorKeypad);
        const clearButton = wrapper.findAll('button').find((b) => b.text() === 'C');
        await clearButton.trigger('click');
        expect(wrapper.emitted('clear')).toBeTruthy();
    });

    it('emits "clearEntry" when CE button is clicked', async () => {
        const wrapper = mount(CalculatorKeypad);
        const ceButton = wrapper.findAll('button').find((b) => b.text() === 'CE');
        await ceButton.trigger('click');
        expect(wrapper.emitted('clearEntry')).toBeTruthy();
    });
});
