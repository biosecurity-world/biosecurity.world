The goal is to remove indecision during development and have some uniformity. If you see a style guide violation, fix it.


* `let` over `const`
    We use `const` for constants only, only hard-coded values that never changes, otherwise we use `let`. Take a look at the use of `const` in the codebase to get a feel for it.
* Variable names for D3 selections
    Variables that hold a D3 selection should start with a `$`.
* No variable named "width", "height", "size", etc.
  Always be very specific, even if the variable's scope is ridiculously small.
* DOM Element, not selections, should end with "El"
