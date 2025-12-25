# Nezumi-PHP

A Ragnarok Online emulator server written in PHP, circa 2003. This project is preserved for historical and educational purposes only.

## Historical Context

This was an actual production server used on a French Ragnarok Online private server. Only the login and character server components were used in production; the map server was never completed.

- **Version**: 2.2
- **Author**: MagicalTux
- **License**: GPLv2
- **Era**: 2003
- **Requirements** (at the time): PHP 4.3.0+, MySQL 3.23.x+, Socket support, Zlib/GZip

## Architecture

The system follows a three-server architecture common to RO emulators:

### Login Server (`login.php`)
Handles client authentication and account management:
- Parses client login packets
- Manages communication with character servers
- Supports auto-registration with `_M`/`_F` gender suffixes
- Multiple password storage methods (plain, MD5, SHA1)

### Character Server (`char.php`)
Manages character selection and data:
- Character creation, deletion, and selection
- Communication between login and map servers
- User count synchronization across servers
- Character authentication tokens

### Map Server (`map.php`) - Discontinued
Was designed for in-game world processing but never completed:
- NPC parsing and scripting
- Game world logic
- Combat and movement

## Directory Structure

```
nezumi-php/
├── login.php, char.php, map.php    # Server entry points
├── config.php                       # Configuration template
├── user_config.php                  # Server-specific overrides
├── src/
│   ├── includes/
│   │   ├── base.php                # Daemon initialization
│   │   ├── login/                  # Login server modules
│   │   ├── char/                   # Character server modules
│   │   ├── map/                    # Map server modules (incomplete)
│   │   ├── common/                 # Shared utilities
│   │   ├── network/                # TCP/UDP socket layer
│   │   ├── database/               # MySQL database layer
│   │   └── mmo/                    # Protocol definitions
│   ├── data/
│   │   ├── config/                 # Game database files (23 files)
│   │   └── npc/                    # NPC definitions
│   └── tools/
│       ├── phpbot/                 # IRC bot with server monitoring
│       ├── sql/                    # Database utilities
│       └── web/                    # Web-based admin tools
```

## Game Data Files

The `src/data/config/` directory contains text-based game databases:

| File | Purpose |
|------|---------|
| `monster2.txt`, `monster10.txt` | Monster definitions |
| `item_db2.txt`, `baitem_db.txt` | Item databases |
| `weapon_info.txt` | Weapon statistics |
| `exp.txt` | Experience tables for job levels |
| `skill_info2.txt` | Skill definitions |
| `npc_warp.txt` | Warp point coordinates |
| `npc_1stjob.txt`, `npc_2ndjob.txt` | Job change NPCs |
| `npc_kafra.txt` | Kafra service NPCs |
| `npc_shop.txt` | Shop NPCs |
| `aggressive-monsters.txt` | Aggressive mob configurations |

## Network Protocol

The server implements the Ragnarok Online packet protocol:
- Hex-based packet IDs with fixed/variable lengths
- Multiple protocol versions (v1, v2, v2.1)
- 68+ distinct packet types

Key packet examples:
- `0x0064`: Client login request
- `0x0065`: Client connected to char server
- `0x0069`: Character server list response
- `0x3710-0x3717`: Character server v2 protocol
- `0x5F00-0x5F07`: Map server v2 protocol

## Technical Implementation

### Daemon Model
- Event-driven architecture with 100ms polling loop
- Main loop: `net_idle()` → `db_check()` → `net_check()`
- Automatic database reconnection with exponential backoff
- Dynamic module loading system

### Binary Data Utilities
Custom functions for network packet handling:
- `readW()`, `readL()`, `readB()` - Read word/long/byte (little-endian)
- `writeW()`, `writeL()`, `writeB()` - Write in little-endian format
- `readIP()`, `writeIP()` - IP address encoding

## Tools

### PHPBot (`src/tools/phpbot/`)
An IRC bot framework with plugins for:
- Google search integration
- Item database lookup
- Server status monitoring
- Uptime tracking

### Data Converters
- `gat_to_ggz.php` - Map format conversion
- `ggz_to_gga.php` - Map format conversion

## Why This Code Should Not Be Used

This codebase is preserved for historical interest only:

1. **Outdated PHP**: Uses deprecated functions (`eregi()`, `each()`, etc.)
2. **Security Issues**: No prepared statements, uses `@` error suppression
3. **No Modern Standards**: Predates PSR standards, modern frameworks, and security practices
4. **Incomplete**: Map server was never finished
5. **Legal Concerns**: Running private RO servers may violate terms of service

## Historical Significance

This project represents an interesting piece of early 2000s game server emulation:
- Demonstrates sophisticated network programming in PHP
- Shows how MMO server architecture was understood at the time
- Contains detailed game data files from the early RO era
- Example of distributed system design before modern tooling
