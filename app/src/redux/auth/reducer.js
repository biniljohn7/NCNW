import authAction from './actions'

const initState = {
    isLogin: false,
    accessToken: null,
    memberId: null,
    firstName: null,
    lastName: null,
    prefix: null,
    profileImage: null,
    referralPoints: 0,
    isProfileCreated: false,
    isNotificationOn: false,
    currentChapter: null,
    chatMemberId: null,
    chatFullName: null,
    chatProfileImage: null,
    userRoles: [],
    membershipStatus: null,
    supporterStatus: null,
}

export default function rootReducer(state = initState, action) {
    switch (action.type) {
        case authAction.LOGIN:
            return {
                ...state,
                ...action.payload,
                isLogin: action.isLogin,
            }
        case authAction.LOGOUT:
            return {
                ...state,
                ...initState,
                isLogin: action.isLogin,
            }
        case authAction.FORGOTPWD:
            return {
                ...state,
                ...initState,
                memberId: action.memberId,
            }
        case authAction.SET:
            return {
                ...state,
                ...action.data,
            }
        case 'UPDATE_PROFILE_IMAGE':
            return {
                ...state,
                profileImage: action.payload,
            };
        default:
            return state
    }
}
