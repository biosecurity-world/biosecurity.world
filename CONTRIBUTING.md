# How to contribute?

* [For developers](#for-developers)


## For developers
**This guide is intended for developers. If you're looking to contribute to the data, check out <LINK_HERE>.**

We use [Nix](https://nixos.org/download/#download-nix), the package manager, to have consistent development environment. Install it, and we recommend you install `nix-direnv` as well.

Then, in any directory with a `flake.nix` file, run:

- If you have nix-direnv installed (recommended), `direnv allow`
- If you don't, `nix develop -c $SHELL`

This drops you in a shell with all the required executables.

If you want to add new packages, guess or search for their names at <https://search.nixos.org/packages>, then look for the "Add dependencies here" in the `flake.nix`, add their names there.

Then either run `direnv reload` or Ctrl-C and run `nix develop`.

If you notice that out-of-date dependencies are causing a problem, you can run `nix flake update`.
