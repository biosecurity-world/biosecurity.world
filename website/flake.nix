{
  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs/nixos-unstable";
  };

  outputs = {nixpkgs, ...} @ inputs: let
    system = "x86_64-linux";
    pkgs = nixpkgs.legacyPackages.${system};
  in {
    devShells.${system}.default = pkgs.mkShell {
      nativeBuildInputs = let
        php = pkgs.php83.buildEnv {
          extraConfig = ''
            memory_limit = 1G
            xdebug.mode=coverage
          '';

          extensions = {
            enabled,
            all,
          }:
            enabled
            ++ (with all; [
              xdebug
            ]);
        };
      in
        [php php.packages.composer]
        ++ (with pkgs; [
          # Add dependencies here
          nodejs
          pnpm
        ]);
    };
  };
}
