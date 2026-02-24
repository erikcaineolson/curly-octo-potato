import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import TickerTape from '../TickerTape.vue';

describe('TickerTape', () => {
    const sampleCalculations = [
        { id: 1, expression: '9 + 3', result: 12 },
        { id: 2, expression: '5 * 4', result: 20 },
    ];

    it('shows "No calculations yet" when list is empty', () => {
        const wrapper = mount(TickerTape, {
            props: { calculations: [] },
        });
        expect(wrapper.text()).toContain('No calculations yet');
    });

    it('renders calculation entries', () => {
        const wrapper = mount(TickerTape, {
            props: { calculations: sampleCalculations },
        });
        expect(wrapper.text()).toContain('9 + 3');
        expect(wrapper.text()).toContain('= 12');
        expect(wrapper.text()).toContain('5 * 4');
        expect(wrapper.text()).toContain('= 20');
    });

    it('formats results with commas', () => {
        const wrapper = mount(TickerTape, {
            props: { calculations: [{ id: 1, expression: '1000+500', result: 1500 }] },
        });
        expect(wrapper.text()).toContain('1,500');
    });

    it('shows Clear All button when there are calculations', () => {
        const wrapper = mount(TickerTape, {
            props: { calculations: sampleCalculations },
        });
        const clearBtn = wrapper.findAll('button').find((b) => b.text() === 'Clear All');
        expect(clearBtn).toBeTruthy();
    });

    it('hides Clear All button when list is empty', () => {
        const wrapper = mount(TickerTape, {
            props: { calculations: [] },
        });
        const clearBtn = wrapper.findAll('button').find((b) => b.text() === 'Clear All');
        expect(clearBtn).toBeUndefined();
    });

    it('emits "clearAll" when Clear All is clicked', async () => {
        const wrapper = mount(TickerTape, {
            props: { calculations: sampleCalculations },
        });
        const clearBtn = wrapper.findAll('button').find((b) => b.text() === 'Clear All');
        await clearBtn.trigger('click');
        expect(wrapper.emitted('clearAll')).toBeTruthy();
    });

    it('emits "deleteOne" with id when delete button is clicked', async () => {
        const wrapper = mount(TickerTape, {
            props: { calculations: sampleCalculations },
        });
        // Delete buttons are the SVG buttons inside each entry
        const deleteButtons = wrapper.findAll('button[title="Delete"]');
        expect(deleteButtons.length).toBe(2);
        await deleteButtons[0].trigger('click');
        expect(wrapper.emitted('deleteOne')).toBeTruthy();
        expect(wrapper.emitted('deleteOne')[0]).toEqual([1]);
    });
});
