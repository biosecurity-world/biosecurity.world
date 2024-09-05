// in tsconfig.json:
// "paths": {
//     "@/*": ["./resources/js/*"]
// }
module.exports = {
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/resources/js/$1',
    },
}
