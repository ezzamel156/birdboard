import Axios from "axios";

class BirdboardForm {
    constructor(data) {

        // this is done so that a deep merge is executed. So that there wont be any reactivity in originalData
        this.originalData = JSON.parse(JSON.stringify(data)); 

        Object.assign(this, data);

        this.errors = {}

        this.submitted = false;
    }

    data() {
        return Object.keys(this.originalData).reduce((data, attribute) => {
            data[attribute] = this[attribute];

            return data;
        }, {});

        // let data = {};

        // for(let attribute in this.originalData) {
        //     data[attribute] = this[attribute];
        // }

        // return data;
    }

    post(endpoint) {
        this.submit(endpoint);
    }

    patch(endpoint) {
        this.submit(endpoint, 'patch');
    }

    delete(endpoint) {
        this.submit(endpoint, 'delete');
    }

    submit(endpoint, requestType = 'post') {
        return axios[requestType](endpoint, this.data())
            .catch(this.onFail.bind(this))
            .then(this.onSuccess.bind(this));
    }

    onFail(error) {
        this.errors = error.response.data.errors;
        this.submitted = false;

        throw error;
    }

    onSuccess(response) {
        this.submitted = true;
        this.errors = {};

        return response;
    }

    reset() {
        Object.assign(this, this.originalData);
    }
}

export default BirdboardForm;