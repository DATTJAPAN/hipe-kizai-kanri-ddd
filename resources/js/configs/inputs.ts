const characterLimit = {
    default: 100,

    // --- profile ---
    name: 100,
    bio: 200,
    displayName: 80,
    tagline: 120,

    // --- credentials ---
    email: 100,
    username: 50,
    password: 50,
    securityQuestion: 150,
    securityAnswer: 100,

    // --- contact ---
    phoneNumber: 15,
    address: 250,
    city: 100,
    postalCode: 20,
    country: 56,

    // --- content ---
    title: 50,
    description: 300,
    remarks: 300,
    comment: 500,
    articleBody: 5000,
    summary: 200,

    // --- system ---
    role: 50,
    permissionName: 100,
    settingKey: 100,
    settingValue: 500,
} as const;

type CharacterLimitKey = keyof typeof characterLimit;

const characterLimitFor = (type: CharacterLimitKey = 'default'): number => characterLimit[type] ?? characterLimit.default;

export { characterLimit, characterLimitFor };
export type { CharacterLimitKey };
