const BASE = '/api';

async function request(method, url, body = null) {
    const options = {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    };

    if (body) {
        options.body = JSON.stringify(body);
    }

    const response = await fetch(`${BASE}${url}`, options);

    if (response.status === 204) {
        return null;
    }

    const data = await response.json();

    if (!response.ok) {
        const error = new Error(data.message || 'Request failed');
        error.status = response.status;
        error.data = data;
        throw error;
    }

    return data;
}

export function fetchCalculations() {
    return request('GET', '/calculations');
}

export function createCalculation(payload) {
    return request('POST', '/calculations', payload);
}

export function deleteCalculation(id) {
    return request('DELETE', `/calculations/${id}`);
}

export function clearAllCalculations() {
    return request('DELETE', '/calculations/all');
}
