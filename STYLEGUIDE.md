The styleguide is for the map rendering codebase, ordered roughly by importance.
* Make things fail as early as possible in the tree drawing code.
* Variable names for D3 selections
    Variables that hold a D3 selection should start with a `$`.
* No variable named "length", "width", "size", etc.
    Always be specific, even if the variable's scope is ridiculously small.
* `let` over `const`
  We use `const` for constants only, i.e., hard-coded values that never changes, otherwise we use `let`. Take a look at the use of `const` in the codebase to get a feel for it.

