import axios from "axios";
import { BASE_URL } from "../helper/constant";
import { setHeaders } from "./apiHelpers";

export const viewMemebership = () => {
    setHeaders();
    return axios.get(`${BASE_URL}/member/?method=dues-view`).then((response) => {
        return response.data;
    });
};

export const getMembershipType = () => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=dues-membership-types-list`)
        .then((response) => {
            return response.data;
        });
};

export const getMembershipPlans = () => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=dues-membership-plans-list`)
        .then((response) => {
            return response.data;
        });
};

export const getInstallments = (id) => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=dues-installments&id=${id}`)
        .then((response) => {
            return response.data;
        });
};

export const getRecievedMemberships = (id) => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=dues-recieved-memberships&id=${id}`)
        .then((response) => {
            return response.data;
        });
};

export const getGiftedMemberships = (id) => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=dues-gifted-memberships&id=${id}`)
        .then((response) => {
            return response.data;
        });
};

export const giftAcceptedApi = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=gift-accepted`, body)
        .then((response) => {
            return response.data;
        });
};

export const giftDeclineApi = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=gift-declined`, body)
        .then((response) => {
            return response.data;
        });
};

export const getAttachment = (id) => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=dues-attachments&type=${id}`)
        .then((response) => {
            return response.data;
        });
};

export const applyCode = (code) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/referral?referralCoupon=${code}`)
        .then((response) => {
            return response.data;
        });
};

export const getMembership = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=payment-summary`, body)
        .then((response) => {
            return response.data;
        });
};

export const addMembership = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=membership-add`, body)
        .then((response) => {
            return response.data;
        });
};

export const validateToken = (token) => {
    // setHeaders()
    return axios
        .get(`${BASE_URL}/public/?method=payment-verify&token=${token}`)
        .then((response) => {
            return response.data;
        });
};

export const fetchStoredCard = (token) => {
    // setHeaders()
    return axios
        .get(`${BASE_URL}/public/?method=dues-fetch-card&token${token}`)
        .then((response) => {
            return response.data;
        });
};

export const deleteStroredCard = (id) => {
    setHeaders();
    return axios.delete(`${BASE_URL}/dues/credit-card/${id}`).then((response) => {
        return response.data;
    });
};

export const createPayment = (body, token) => {
    // setHeaders()
    return axios
        .post(`${BASE_URL}/member/payment/${token}`, body)
        .then((response) => {
            return response.data;
        });
};

export const fetchPayment = (id) => {
    return axios.get(`${BASE_URL}/dues/payment/${id}`).then((response) => {
        return response.data;
    });
};

export const cancelPayment = (id) => {
    return axios.delete(`${BASE_URL}/dues/payment/${id}`).then((response) => {
        return response.data;
    });
};

export const cancelSubscription = (id) => {
    return axios
        .delete(`${BASE_URL}/dues/cancel-payment/${id}`)
        .then((response) => {
            return response.data;
        });
};

export const viewPaymentHistory = (id) => {
    return axios
        .get(`${BASE_URL}/member/?method=dues-history`)
        .then((response) => {
            return response.data;
        });
};

export const getDropdown = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/public/?method=dropdown-list`, body)
        .then((response) => {
            return response.data;
        });
};

export const getAllMembers = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=get-all-member`, body)
        .then((response) => {
            return response.data;
        });
};
/** */
export const declineGift = (data) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=get-all-members`, data)
        .then((response) => {
            return { success: 1, msg: "Gift Membership" };
        });
};

export const getExpiredMemberships = () => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=get-exp-memberships`)
        .then((response) => {
            return response.data;
        });
};

export const duesNewMember = (body) => {
    return axios
        .post(BASE_URL + "/member/?method=dues-add-new-member", body)
        .then((response) => {
            return response.data;
        });
};

export const renewExpiredMembership = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=membership-recurring-txn`, body)
        .then((response) => {
            return response.data;
        });
};
export const duesSearchSections = (key) => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=get-sections&&key=${key}`)
        .then((response) => {
            return response.data;
        });
};

export const duesSearchAffiliate = (key) => {
    setHeaders();
    return axios
        .get(`${BASE_URL}/member/?method=get-affiliates&&key=${key}`)
        .then((response) => {
            return response.data;
        });
};

export const uploadOfflineProof = (body) => {
    setHeaders();
    return axios
        .post(`${BASE_URL}/member/?method=paymnt-offline-proof`, body)
        .then((response) => {
            return response.data;
        });
};
